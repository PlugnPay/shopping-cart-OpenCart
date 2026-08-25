<?php
namespace Opencart\Catalog\Model\Extension\PlugnpayApiAch\Payment;

/**
 * Class PlugnpayAch
 *
 * @package Opencart\Catalog\Model\Extension\PlugnpayApiAch\Payment
 */
class PlugnpayAch extends \Opencart\System\Engine\Model {
	/**
	 * Get Methods
	 *
	 * @param array<string, mixed> $address array of data
	 *
	 * @return array<string, mixed>
	 */
	public function getMethods(array $address = []): array {
		$this->load->language('extension/plugnpay_api_ach/payment/plugnpay_ach');

		$status = true;

		if (trim((string)$this->config->get('payment_plugnpay_ach_merchant')) === '' || trim((string)$this->config->get('payment_plugnpay_ach_password')) === '') {
			$status = false;
		} elseif (!function_exists('curl_init')) {
			$status = false;
		} elseif (!$this->isHttps()) {
			$status = false;
		} elseif ($this->cart->hasSubscription()) {
			$status = false;
		} elseif ((float)$this->config->get('payment_plugnpay_ach_total') > 0 && (float)$this->config->get('payment_plugnpay_ach_total') > $this->cart->getTotal()) {
			$status = false;
		} elseif (!$this->config->get('config_checkout_payment_address')) {
			$status = true;
		} elseif (!$this->config->get('payment_plugnpay_ach_geo_zone_id')) {
			$status = true;
		} else {
			$this->load->model('localisation/geo_zone');

			$results = $this->model_localisation_geo_zone->getGeoZone((int)$this->config->get('payment_plugnpay_ach_geo_zone_id'), (int)($address['country_id'] ?? 0), (int)($address['zone_id'] ?? 0));

			$status = (bool)$results;
		}

		$method_data = [];

		if ($status) {
			$option_data['plugnpay_ach'] = [
				'code' => 'plugnpay_ach.plugnpay_ach',
				'name' => $this->language->get('heading_title')
			];

			$method_data = [
				'code'       => 'plugnpay_ach',
				'name'       => $this->language->get('heading_title'),
				'option'     => $option_data,
				'sort_order' => $this->config->get('payment_plugnpay_ach_sort_order')
			];
		}

		return $method_data;
	}

	private function isHttps(): bool {
		if ($this->config->get('config_secure')) {
			return true;
		}

		$https = $this->request->server['HTTPS'] ?? '';

		if ($https && $https !== 'off') {
			return true;
		}

		return (string)($this->request->server['SERVER_PORT'] ?? '') === '443';
	}
}
