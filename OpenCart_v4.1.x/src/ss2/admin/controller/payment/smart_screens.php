<?php
namespace Opencart\Admin\Controller\Extension\PlugnpaySs2\Payment;

/**
 * Class SmartScreens
 *
 * Filename and class must not contain digits. OpenCart 4 maps class names to
 * files with preg_replace('~([a-z])([A-Z]|[0-9])~', '\1_\2', ...), so
 * PlugnpaySs2 would load payment/plugnpay_ss_2.php and 404.
 *
 * @package Opencart\Admin\Controller\Extension\PlugnpaySs2\Payment
 */
class SmartScreens extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return void
	 */
	public function index(): void {
		$this->load->language('extension/plugnpay_ss2/payment/smart_screens');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/plugnpay_ss2/payment/smart_screens', 'user_token=' . $this->session->data['user_token'])
		];

		$data['save'] = $this->url->link('extension/plugnpay_ss2/payment/smart_screens.save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment');

		$data['payment_smart_screens_merchant'] = $this->config->get('payment_smart_screens_merchant');
		$data['payment_smart_screens_total'] = $this->config->get('payment_smart_screens_total');
		$data['payment_smart_screens_debug'] = $this->config->get('payment_smart_screens_debug');

		$catalog_url = defined('HTTP_CATALOG') ? HTTP_CATALOG : (string)$this->config->get('config_url');

		if ($this->config->get('config_secure')) {
			$catalog_url = str_replace('http://', 'https://', $catalog_url);
		}

		$data['payment_smart_screens_callback'] = rtrim($catalog_url, '/') . '/index.php?route=extension/plugnpay_ss2/payment/smart_screens.callback';

		$data['payment_smart_screens_order_status_id'] = (int)$this->config->get('payment_smart_screens_order_status_id');

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['payment_smart_screens_geo_zone_id'] = $this->config->get('payment_smart_screens_geo_zone_id');

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$data['payment_smart_screens_status'] = $this->config->get('payment_smart_screens_status');
		$data['payment_smart_screens_sort_order'] = $this->config->get('payment_smart_screens_sort_order');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/plugnpay_ss2/payment/smart_screens', $data));
	}

	/**
	 * Save
	 *
	 * @return void
	 */
	public function save(): void {
		$this->load->language('extension/plugnpay_ss2/payment/smart_screens');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/plugnpay_ss2/payment/smart_screens')) {
			$json['error']['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->post['payment_smart_screens_merchant'])) {
			$json['error']['merchant'] = $this->language->get('error_merchant');
		}

		if (!$json) {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('payment_smart_screens', $this->request->post);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
