<?php
namespace Opencart\Catalog\Model\Extension\PlugnpaySs2\Payment;

/**
 * Class SmartScreens
 *
 * Can be called from $this->load->model('extension/plugnpay_ss2/payment/smart_screens');
 *
 * @package Opencart\Catalog\Model\Extension\PlugnpaySs2\Payment
 */
class SmartScreens extends \Opencart\System\Engine\Model {
	/**
	 * Get Methods
	 *
	 * @param array<string, mixed> $address array of data
	 *
	 * @return array<string, mixed>
	 */
	public function getMethods(array $address = []): array {
		$this->load->language('extension/plugnpay_ss2/payment/smart_screens');

		$status = true;

		if (trim((string)$this->config->get('payment_smart_screens_merchant')) === '') {
			$status = false;
		} elseif ($this->cart->hasSubscription()) {
			$status = false;
		} elseif ((float)$this->config->get('payment_smart_screens_total') > 0 && (float)$this->config->get('payment_smart_screens_total') > $this->cart->getTotal()) {
			$status = false;
		} elseif (!$this->config->get('config_checkout_payment_address')) {
			$status = true;
		} elseif (!$this->config->get('payment_smart_screens_geo_zone_id')) {
			$status = true;
		} else {
			$this->load->model('localisation/geo_zone');

			$results = $this->model_localisation_geo_zone->getGeoZone((int)$this->config->get('payment_smart_screens_geo_zone_id'), (int)($address['country_id'] ?? 0), (int)($address['zone_id'] ?? 0));

			$status = (bool)$results;
		}

		$method_data = [];

		if ($status) {
			$option_data['smart_screens'] = [
				'code' => 'smart_screens.smart_screens',
				'name' => $this->language->get('heading_title')
			];

			$method_data = [
				'code'       => 'smart_screens',
				'name'       => $this->language->get('heading_title'),
				'option'     => $option_data,
				'sort_order' => $this->config->get('payment_smart_screens_sort_order')
			];
		}

		return $method_data;
	}
}
