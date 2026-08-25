<?php
namespace Opencart\Admin\Controller\Extension\PlugnpayApiCc\Payment;

/**
 * Class PlugnpayCc
 *
 * Filename and class must not contain digits.
 *
 * @package Opencart\Admin\Controller\Extension\PlugnpayApiCc\Payment
 */
class PlugnpayCc extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return void
	 */
	public function index(): void {
		$this->load->language('extension/plugnpay_api_cc/payment/plugnpay_cc');

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
			'href' => $this->url->link('extension/plugnpay_api_cc/payment/plugnpay_cc', 'user_token=' . $this->session->data['user_token'])
		];

		$data['save'] = $this->url->link('extension/plugnpay_api_cc/payment/plugnpay_cc.save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment');

		$data['payment_plugnpay_cc_merchant'] = $this->config->get('payment_plugnpay_cc_merchant');
		$data['payment_plugnpay_cc_password'] = $this->config->get('payment_plugnpay_cc_password');
		$data['payment_plugnpay_cc_authtype'] = $this->config->get('payment_plugnpay_cc_authtype') ?: 'authonly';
		$use_cvv = $this->config->get('payment_plugnpay_cc_use_cvv');
		$data['payment_plugnpay_cc_use_cvv'] = ($use_cvv === null || $use_cvv === '') ? 1 : (int)$use_cvv;
		$data['payment_plugnpay_cc_total'] = $this->config->get('payment_plugnpay_cc_total');
		$data['payment_plugnpay_cc_debug'] = $this->config->get('payment_plugnpay_cc_debug');
		$data['payment_plugnpay_cc_order_status_id'] = (int)$this->config->get('payment_plugnpay_cc_order_status_id');

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['payment_plugnpay_cc_geo_zone_id'] = $this->config->get('payment_plugnpay_cc_geo_zone_id');

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$data['payment_plugnpay_cc_status'] = $this->config->get('payment_plugnpay_cc_status');
		$data['payment_plugnpay_cc_sort_order'] = $this->config->get('payment_plugnpay_cc_sort_order');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/plugnpay_api_cc/payment/plugnpay_cc', $data));
	}

	/**
	 * Save
	 *
	 * @return void
	 */
	public function save(): void {
		$this->load->language('extension/plugnpay_api_cc/payment/plugnpay_cc');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/plugnpay_api_cc/payment/plugnpay_cc')) {
			$json['error']['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->post['payment_plugnpay_cc_merchant'])) {
			$json['error']['merchant'] = $this->language->get('error_merchant');
		}

		if (empty($this->request->post['payment_plugnpay_cc_password'])) {
			$json['error']['password'] = $this->language->get('error_password');
		}

		if (!$json) {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('payment_plugnpay_cc', $this->request->post);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
