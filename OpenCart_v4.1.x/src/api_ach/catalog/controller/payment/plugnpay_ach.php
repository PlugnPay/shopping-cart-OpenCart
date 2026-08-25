<?php
namespace Opencart\Catalog\Controller\Extension\PlugnpayApiAch\Payment;

use Opencart\System\Library\Extension\PlugnpayApiAch\RemoteApi;
use Opencart\System\Library\Extension\PlugnpayApiAch\RemoteApiLogger;

/**
 * Class PlugnpayAch
 *
 * @package Opencart\Catalog\Controller\Extension\PlugnpayApiAch\Payment
 */
class PlugnpayAch extends \Opencart\System\Engine\Controller {
	/**
	 * Index — onsite ACH form.
	 *
	 * @return string
	 */
	public function index(): string {
		$this->load->language('extension/plugnpay_api_ach/payment/plugnpay_ach');

		$account_name = '';

		if (!empty($this->session->data['order_id'])) {
			$this->load->model('checkout/order');

			$order_info = $this->model_checkout_order->getOrder((int)$this->session->data['order_id']);

			if ($order_info) {
				$account_name = trim(($order_info['payment_firstname'] ?? '') . ' ' . ($order_info['payment_lastname'] ?? ''));
			}
		}

		$data['account_name'] = $account_name;
		$data['language'] = $this->config->get('config_language');
		$data['button_confirm'] = $this->language->get('button_confirm');

		return $this->load->view('extension/plugnpay_api_ach/payment/plugnpay_ach', $data);
	}

	/**
	 * Confirm — POST bank data to pnpremote.cgi.
	 *
	 * @return void
	 */
	public function confirm(): void {
		$this->load->language('extension/plugnpay_api_ach/payment/plugnpay_ach');

		$json = [];

		if (!isset($this->session->data['order_id'])) {
			$json['error'] = $this->language->get('error_order');
		}

		if (!isset($this->session->data['payment_method']) || $this->session->data['payment_method']['code'] != 'plugnpay_ach.plugnpay_ach') {
			$json['error'] = $this->language->get('error_payment_method');
		}

		$account_name = trim((string)($this->request->post['ach_owner'] ?? ''));
		$accttype = strtolower(trim((string)($this->request->post['ach_accttype'] ?? '')));
		$routing = preg_replace('/\D/', '', (string)($this->request->post['ach_routingnum'] ?? '')) ?? '';
		$account = preg_replace('/\D/', '', (string)($this->request->post['ach_accountnum'] ?? '')) ?? '';
		$checknum = preg_replace('/\D/', '', (string)($this->request->post['ach_checknum'] ?? '')) ?? '';
		$business = !empty($this->request->post['ach_acctclass']);

		if (!$json && $account_name === '') {
			$json['error'] = $this->language->get('error_ach_owner');
		}

		if (!$json && !in_array($accttype, ['checking', 'savings'], true)) {
			$json['error'] = $this->language->get('error_ach_accttype');
		}

		if (!$json && strlen($routing) !== 9) {
			$json['error'] = $this->language->get('error_ach_routingnum');
		}

		if (!$json && strlen($account) < 4) {
			$json['error'] = $this->language->get('error_ach_accountnum');
		}

		if (!$json && $checknum === '') {
			$json['error'] = $this->language->get('error_ach_checknum');
		}

		$order_info = [];

		if (!$json) {
			$this->load->model('checkout/order');

			$order_info = $this->model_checkout_order->getOrder((int)$this->session->data['order_id']);

			if (!$order_info) {
				$json['error'] = $this->language->get('error_order');
			}
		}

		if (!$json) {
			$amount = $this->currency->format((float)$order_info['total'], $order_info['currency_code'], (float)$order_info['currency_value'], false);

			$fields = RemoteApi::buildAchFields([
				'authtype'      => (string)$this->config->get('payment_plugnpay_ach_authtype'),
				'amount'        => (string)$amount,
				'currency'      => (string)$order_info['currency_code'],
				'order_id'      => (string)(int)$order_info['order_id'],
				'account_name'  => $account_name,
				'accttype'      => $accttype,
				'routingnum'    => $routing,
				'accountnum'    => $account,
				'checknum'      => $checknum,
				'business'      => $business,
				'card_company'  => (string)($order_info['payment_company'] ?? ''),
				'card_address1' => (string)($order_info['payment_address_1'] ?? ''),
				'card_address2' => (string)($order_info['payment_address_2'] ?? ''),
				'card_city'     => (string)($order_info['payment_city'] ?? ''),
				'card_state'    => (string)($order_info['payment_zone_code'] ?? $order_info['payment_zone'] ?? ''),
				'card_zip'      => (string)($order_info['payment_postcode'] ?? ''),
				'card_country'  => (string)($order_info['payment_iso_code_2'] ?? $order_info['payment_country'] ?? ''),
				'phone'         => (string)($order_info['telephone'] ?? ''),
				'email'         => (string)($order_info['email'] ?? ''),
				'ship_name'     => trim(($order_info['shipping_firstname'] ?? '') . ' ' . ($order_info['shipping_lastname'] ?? '')),
				'ship_address1' => (string)($order_info['shipping_address_1'] ?? ''),
				'ship_address2' => (string)($order_info['shipping_address_2'] ?? ''),
				'ship_city'     => (string)($order_info['shipping_city'] ?? ''),
				'ship_state'    => (string)($order_info['shipping_zone_code'] ?? $order_info['shipping_zone'] ?? ''),
				'ship_zip'      => (string)($order_info['shipping_postcode'] ?? ''),
				'ship_country'  => (string)($order_info['shipping_iso_code_2'] ?? $order_info['shipping_country'] ?? ''),
				'ip_address'    => (string)($order_info['ip'] ?? $this->request->server['REMOTE_ADDR'] ?? ''),
				'items'         => $this->lineItems(),
			]);

			$api = new RemoteApi(
				(string)$this->config->get('payment_plugnpay_ach_merchant'),
				(string)$this->config->get('payment_plugnpay_ach_password'),
				$this->logger()
			);

			$response = $api->request($fields);

			if ($api->isApproved($response)) {
				$message = 'ACH/eCheck via PlugnPay Remote API. AUTH: ' . ($response['auth-code'] ?? '')
					. ' TransID/orderID: ' . ($response['orderID'] ?? '');

				$status_id = $this->approvedOrderStatusId();

				$this->model_checkout_order->addHistory((int)$order_info['order_id'], $status_id, $message, true);

				$json['redirect'] = $this->url->link('checkout/success', 'language=' . $this->config->get('config_language'), true);
			} else {
				$error = trim((string)($response['MErrMsg'] ?? ''));

				$json['error'] = $error !== '' ? $error : $this->language->get('error_declined');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * @return list<array<string, mixed>>
	 */
	private function lineItems(): array {
		$items = [];

		foreach ($this->cart->getProducts() as $product) {
			$items[] = [
				'sku'      => (string)($product['model'] ?? ''),
				'price'    => $this->currency->format((float)$product['price'], $this->session->data['currency'] ?? $this->config->get('config_currency'), (float)$this->currency->getValue($this->session->data['currency'] ?? $this->config->get('config_currency')), false),
				'quantity' => (int)($product['quantity'] ?? 1),
				'name'     => (string)($product['name'] ?? ''),
			];
		}

		return $items;
	}

	private function approvedOrderStatusId(): int {
		$this->load->model('localisation/order_status');

		return RemoteApi::resolveOrderStatusId(
			(int)$this->config->get('payment_plugnpay_ach_order_status_id'),
			$this->model_localisation_order_status->getOrderStatuses(),
			(int)$this->config->get('config_order_status_id')
		);
	}

	private function logger(): RemoteApiLogger {
		$log_dir = defined('DIR_LOGS') ? DIR_LOGS : (defined('DIR_STORAGE') ? DIR_STORAGE . 'logs' : sys_get_temp_dir());

		return new RemoteApiLogger($log_dir, (bool)$this->config->get('payment_plugnpay_ach_debug'), 'plugnpay_api_ach');
	}
}
