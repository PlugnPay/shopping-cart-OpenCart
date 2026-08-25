<?php
namespace Opencart\Catalog\Controller\Extension\PlugnpayApiCc\Payment;

use Opencart\System\Library\Extension\PlugnpayApiCc\RemoteApi;
use Opencart\System\Library\Extension\PlugnpayApiCc\RemoteApiLogger;

/**
 * Class PlugnpayCc
 *
 * @package Opencart\Catalog\Controller\Extension\PlugnpayApiCc\Payment
 */
class PlugnpayCc extends \Opencart\System\Engine\Controller {
	/**
	 * Index — onsite card form.
	 *
	 * @return string
	 */
	public function index(): string {
		$this->load->language('extension/plugnpay_api_cc/payment/plugnpay_cc');

		$months = [];

		for ($i = 1; $i <= 12; $i++) {
			$months[] = [
				'text'  => date('F', mktime(0, 0, 0, $i, 1, 2000)),
				'value' => sprintf('%02d', $i)
			];
		}

		$year_expire = [];
		$start = (int)date('Y');

		for ($i = $start; $i < $start + 11; $i++) {
			$year_expire[] = [
				'text'  => (string)$i,
				'value' => substr((string)$i, -2)
			];
		}

		$card_name = '';

		if (!empty($this->session->data['order_id'])) {
			$this->load->model('checkout/order');

			$order_info = $this->model_checkout_order->getOrder((int)$this->session->data['order_id']);

			if ($order_info) {
				$card_name = trim(($order_info['payment_firstname'] ?? '') . ' ' . ($order_info['payment_lastname'] ?? ''));
			}
		}

		$data['months'] = $months;
		$data['year_expire'] = $year_expire;
		$data['card_name'] = $card_name;
		$use_cvv = $this->config->get('payment_plugnpay_cc_use_cvv');
		$data['use_cvv'] = ($use_cvv === null || $use_cvv === '') ? true : (bool)$use_cvv;
		$data['language'] = $this->config->get('config_language');
		$data['button_confirm'] = $this->language->get('button_confirm');

		return $this->load->view('extension/plugnpay_api_cc/payment/plugnpay_cc', $data);
	}

	/**
	 * Confirm — POST card data to pnpremote.cgi.
	 *
	 * @return void
	 */
	public function confirm(): void {
		$this->load->language('extension/plugnpay_api_cc/payment/plugnpay_cc');

		$json = [];

		if (!isset($this->session->data['order_id'])) {
			$json['error'] = $this->language->get('error_order');
		}

		if (!isset($this->session->data['payment_method']) || $this->session->data['payment_method']['code'] != 'plugnpay_cc.plugnpay_cc') {
			$json['error'] = $this->language->get('error_payment_method');
		}

		$card_name = trim((string)($this->request->post['cc_owner'] ?? ''));
		$card_number = preg_replace('/\D/', '', (string)($this->request->post['cc_number'] ?? '')) ?? '';
		$exp_month = (string)($this->request->post['cc_expire_date_month'] ?? '');
		$exp_year = (string)($this->request->post['cc_expire_date_year'] ?? '');
		$cvv = trim((string)($this->request->post['cc_cvv2'] ?? ''));

		if (!$json && $card_name === '') {
			$json['error'] = $this->language->get('error_cc_owner');
		}

		if (!$json && (strlen($card_number) < 13 || strlen($card_number) > 19)) {
			$json['error'] = $this->language->get('error_cc_number');
		}

		if (!$json && (!preg_match('/^\d{2}$/', $exp_month) || !preg_match('/^\d{2}$/', $exp_year))) {
			$json['error'] = $this->language->get('error_cc_expire');
		}

		$use_cvv = $this->config->get('payment_plugnpay_cc_use_cvv');
		$require_cvv = ($use_cvv === null || $use_cvv === '') ? true : (bool)$use_cvv;

		if (!$json && $require_cvv && $cvv === '') {
			$json['error'] = $this->language->get('error_cc_cvv');
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

			$fields = RemoteApi::buildCreditFields([
				'authtype'        => (string)$this->config->get('payment_plugnpay_cc_authtype'),
				'amount'          => (string)$amount,
				'currency'        => (string)$order_info['currency_code'],
				'order_id'        => (string)(int)$order_info['order_id'],
				'card_number'     => $card_number,
				'card_exp'        => $exp_month . '/' . $exp_year,
				'card_name'       => $card_name,
				'card_cvv'        => $require_cvv ? $cvv : '',
				'card_company'    => (string)($order_info['payment_company'] ?? ''),
				'card_address1'   => (string)($order_info['payment_address_1'] ?? ''),
				'card_address2'   => (string)($order_info['payment_address_2'] ?? ''),
				'card_city'       => (string)($order_info['payment_city'] ?? ''),
				'card_state'      => (string)($order_info['payment_zone_code'] ?? $order_info['payment_zone'] ?? ''),
				'card_zip'        => (string)($order_info['payment_postcode'] ?? ''),
				'card_country'    => (string)($order_info['payment_iso_code_2'] ?? $order_info['payment_country'] ?? ''),
				'phone'           => (string)($order_info['telephone'] ?? ''),
				'email'           => (string)($order_info['email'] ?? ''),
				'ship_name'       => trim(($order_info['shipping_firstname'] ?? '') . ' ' . ($order_info['shipping_lastname'] ?? '')),
				'ship_address1'   => (string)($order_info['shipping_address_1'] ?? ''),
				'ship_address2'   => (string)($order_info['shipping_address_2'] ?? ''),
				'ship_city'       => (string)($order_info['shipping_city'] ?? ''),
				'ship_state'      => (string)($order_info['shipping_zone_code'] ?? $order_info['shipping_zone'] ?? ''),
				'ship_zip'        => (string)($order_info['shipping_postcode'] ?? ''),
				'ship_country'    => (string)($order_info['shipping_iso_code_2'] ?? $order_info['shipping_country'] ?? ''),
				'ip_address'      => (string)($order_info['ip'] ?? $this->request->server['REMOTE_ADDR'] ?? ''),
				'items'           => $this->lineItems(),
			]);

			$api = new RemoteApi(
				(string)$this->config->get('payment_plugnpay_cc_merchant'),
				(string)$this->config->get('payment_plugnpay_cc_password'),
				$this->logger()
			);

			$response = $api->request($fields);

			if ($api->isApproved($response)) {
				$message = 'Credit Card via PlugnPay Remote API. AUTH: ' . ($response['auth-code'] ?? '')
					. ' TransID/orderID: ' . ($response['orderID'] ?? '')
					. ' AVS: ' . ($response['avs-code'] ?? '')
					. ' CVV: ' . ($response['cvvresp'] ?? '');

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
			(int)$this->config->get('payment_plugnpay_cc_order_status_id'),
			$this->model_localisation_order_status->getOrderStatuses(),
			(int)$this->config->get('config_order_status_id')
		);
	}

	private function logger(): RemoteApiLogger {
		$log_dir = defined('DIR_LOGS') ? DIR_LOGS : (defined('DIR_STORAGE') ? DIR_STORAGE . 'logs' : sys_get_temp_dir());

		return new RemoteApiLogger($log_dir, (bool)$this->config->get('payment_plugnpay_cc_debug'), 'plugnpay_api_cc');
	}
}
