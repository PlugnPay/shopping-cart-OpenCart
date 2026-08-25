<?php
namespace Opencart\Catalog\Controller\Extension\PlugnpaySs2\Payment;

use Opencart\System\Library\Extension\PlugnpaySs2\SmartScreensLogger;
use Opencart\System\Library\Extension\PlugnpaySs2\SmartScreensProtocol;

/**
 * Class SmartScreens
 *
 * Filename and class must not contain digits. OpenCart 4 maps class names to
 * files with preg_replace('~([a-z])([A-Z]|[0-9])~', '\1_\2', ...), so
 * PlugnpaySs2 would load payment/plugnpay_ss_2.php and 404.
 *
 * @package Opencart\Catalog\Controller\Extension\PlugnpaySs2\Payment
 */
class SmartScreens extends \Opencart\System\Engine\Controller {
	/**
	 * Index — hosted POST form to PlugnPay Smart Screens v2.
	 *
	 * @return string
	 */
	public function index(): string {
		$this->load->language('extension/plugnpay_ss2/payment/smart_screens');

		if (empty($this->session->data['order_id'])) {
			return $this->load->view('extension/plugnpay_ss2/payment/smart_screens', [
				'error' => $this->language->get('error_order')
			]);
		}

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder((int)$this->session->data['order_id']);

		if (!$order_info) {
			return $this->load->view('extension/plugnpay_ss2/payment/smart_screens', [
				'error' => $this->language->get('error_order')
			]);
		}

		$order_id = (string)(int)$order_info['order_id'];
		$session_id = (string)session_id();
		$amount = $this->currency->format((float)$order_info['total'], $order_info['currency_code'], (float)$order_info['currency_value'], false);

		$success_url = $this->url->link(
			'extension/plugnpay_ss2/payment/smart_screens.callback',
			'language=' . $this->config->get('config_language') . '&order_id=' . urlencode($order_id),
			true
		);

		$fields = SmartScreensProtocol::buildHostedFields([
			'gateway_account'    => (string)$this->config->get('payment_smart_screens_merchant'),
			'amount'             => (string)$amount,
			'currency'           => (string)$order_info['currency_code'],
			'order_id'           => $order_id,
			'session_id'         => $session_id,
			'success_url'        => $success_url,
			'billing_company'    => (string)($order_info['payment_company'] ?? ''),
			'billing_name'       => trim(($order_info['payment_firstname'] ?? '') . ' ' . ($order_info['payment_lastname'] ?? '')),
			'billing_address_1'  => (string)($order_info['payment_address_1'] ?? ''),
			'billing_address_2'  => (string)($order_info['payment_address_2'] ?? ''),
			'billing_city'       => (string)($order_info['payment_city'] ?? ''),
			'billing_state'      => (string)($order_info['payment_zone_code'] ?? $order_info['payment_zone'] ?? ''),
			'billing_postal_code'=> (string)($order_info['payment_postcode'] ?? ''),
			'billing_country'    => (string)($order_info['payment_iso_code_2'] ?? $order_info['payment_country'] ?? ''),
			'billing_phone'      => (string)($order_info['telephone'] ?? ''),
			'billing_email'      => (string)($order_info['email'] ?? ''),
			'ip_address'         => (string)($order_info['ip'] ?? $this->request->server['REMOTE_ADDR'] ?? ''),
		]);

		$this->session->data['smart_screens_expected'] = [
			'amount'   => (string)$amount,
			'account'  => trim((string)$this->config->get('payment_smart_screens_merchant')),
			'session'  => $session_id,
			'order_id' => $order_id,
		];

		$this->logger()->log('Submit-Data', $fields);

		$data['action'] = SmartScreensProtocol::ENDPOINT;
		$data['hosted_fields'] = $fields;
		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['text_redirect'] = $this->language->get('text_redirect');

		return $this->load->view('extension/plugnpay_ss2/payment/smart_screens', $data);
	}

	/**
	 * Callback — PlugnPay POSTs here after hosted checkout.
	 *
	 * @return void
	 */
	public function callback(): void {
		$this->load->language('extension/plugnpay_ss2/payment/smart_screens');

		$response = array_merge($this->request->get, $this->request->post);
		$this->logger()->log('Response-Data', $response);

		$order_id = $this->resolveOrderId($response);

		if ($order_id < 1) {
			$this->response->redirect($this->url->link('checkout/failure', 'language=' . $this->config->get('config_language'), true));
			return;
		}

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($order_id);

		if (!$order_info) {
			$this->response->redirect($this->url->link('checkout/failure', 'language=' . $this->config->get('config_language'), true));
			return;
		}

		if (!SmartScreensProtocol::isSuccessfulResponse($response)) {
			$comment = 'PlugnPay Smart Screens v2 declined: ' . (string)($response['pi_response_status'] ?? 'empty');

			if (!empty($response['pi_error_message'])) {
				$comment .= ' — ' . $response['pi_error_message'];
			}

			$this->model_checkout_order->addHistory($order_id, (int)$order_info['order_status_id'], $comment, false);
			$this->response->redirect($this->url->link('checkout/failure', 'language=' . $this->config->get('config_language'), true));
			return;
		}

		if (!$this->validateReturn($response, $order_info)) {
			$this->response->redirect($this->url->link('checkout/failure', 'language=' . $this->config->get('config_language'), true));
			return;
		}

		$auth_code = (string)($response['pt_authorization_code'] ?? '');
		$trans_id = (string)($response['pt_order_id'] ?? '');
		$message = 'Credit Card authorization via PlugnPay Smart Screens v2. AUTH: ' . $auth_code
			. ' TransID/orderID: ' . $trans_id
			. ' (auth-only; settle in PlugnPay Admin)';

		$status_id = (int)$this->config->get('payment_smart_screens_order_status_id');

		if ($status_id < 1) {
			$status_id = 1;
		}

		$this->model_checkout_order->addHistory($order_id, $status_id, $message, true);

		if (isset($this->session->data['order_id'])) {
			$this->cart->clear();

			unset($this->session->data['order_id']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['comment']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
			unset($this->session->data['totals']);
			unset($this->session->data['smart_screens_expected']);
		}

		$this->logger()->log('Return approved', ['order_id' => $order_id, 'trans_id' => $trans_id]);

		$this->response->redirect($this->url->link('checkout/success', 'language=' . $this->config->get('config_language'), true));
	}

	/**
	 * @param array<string, mixed> $response
	 */
	private function resolveOrderId(array $response): int {
		$from_custom = SmartScreensProtocol::extractCustomValue($response, SmartScreensProtocol::CUSTOM_ORDER_ID);

		if ($from_custom !== '' && ctype_digit($from_custom)) {
			return (int)$from_custom;
		}

		if (!empty($response['pt_account_code_1']) && ctype_digit((string)$response['pt_account_code_1'])) {
			return (int)$response['pt_account_code_1'];
		}

		if (!empty($this->request->get['order_id']) && ctype_digit((string)$this->request->get['order_id'])) {
			return (int)$this->request->get['order_id'];
		}

		if (!empty($this->session->data['order_id'])) {
			return (int)$this->session->data['order_id'];
		}

		return 0;
	}

	/**
	 * @param array<string, mixed>              $response
	 * @param array<string, mixed>              $order_info
	 */
	private function validateReturn(array $response, array $order_info): bool {
		$expected_account = trim((string)$this->config->get('payment_smart_screens_merchant'));
		$returned_account = trim((string)($response['pt_gateway_account'] ?? ''));

		if ($expected_account === '' || strcasecmp($returned_account, $expected_account) !== 0) {
			$this->logger()->log('Account mismatch', ['expected' => $expected_account, 'returned' => $returned_account]);

			return false;
		}

		$expected_amount = $this->currency->format((float)$order_info['total'], $order_info['currency_code'], (float)$order_info['currency_value'], false);
		$returned_amount = $response['pt_transaction_amount'] ?? '';

		if (!SmartScreensProtocol::amountsMatch($returned_amount, $expected_amount)) {
			$this->logger()->log('Amount mismatch', ['expected' => $expected_amount, 'returned' => $returned_amount]);

			return false;
		}

		$returned_order = SmartScreensProtocol::extractCustomValue($response, SmartScreensProtocol::CUSTOM_ORDER_ID);

		if ($returned_order === '') {
			$returned_order = (string)($response['pt_account_code_1'] ?? '');
		}

		if ((string)(int)$order_info['order_id'] !== (string)$returned_order) {
			$this->logger()->log('Order mismatch', ['expected' => $order_info['order_id'], 'returned' => $returned_order]);

			return false;
		}

		$returned_session = SmartScreensProtocol::extractCustomValue($response, SmartScreensProtocol::CUSTOM_SESSION);
		$current_session = (string)session_id();

		if ($returned_session !== '' && $current_session !== '' && !hash_equals($returned_session, $current_session)) {
			// Session cookie may be missing after the cross-site POST (SameSite).
			// Order id + amount + account still have to match above.
			$this->logger()->log('Session cookie mismatch (continuing with order checks)', [
				'returned' => $returned_session
			]);
		}

		return true;
	}

	private function logger(): SmartScreensLogger {
		$log_dir = defined('DIR_LOGS') ? DIR_LOGS : (defined('DIR_STORAGE') ? DIR_STORAGE . 'logs' : sys_get_temp_dir());

		return new SmartScreensLogger($log_dir, (bool)$this->config->get('payment_smart_screens_debug'));
	}
}
