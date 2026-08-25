<?php
namespace Opencart\Admin\Controller\Extension\PlugnpayApiAch\Payment;

use Opencart\System\Library\Extension\PlugnpayApiAch\RemoteApi;

/**
 * Class PlugnpayAch
 *
 * Filename and class must not contain digits.
 *
 * @package Opencart\Admin\Controller\Extension\PlugnpayApiAch\Payment
 */
class PlugnpayAch extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return void
	 */
	public function index(): void {
		$this->load->language('extension/plugnpay_api_ach/payment/plugnpay_ach');

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
			'href' => $this->url->link('extension/plugnpay_api_ach/payment/plugnpay_ach', 'user_token=' . $this->session->data['user_token'])
		];

		$data['save'] = $this->url->link('extension/plugnpay_api_ach/payment/plugnpay_ach.save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment');

		$data['payment_plugnpay_ach_merchant'] = $this->config->get('payment_plugnpay_ach_merchant');
		$data['payment_plugnpay_ach_password'] = $this->config->get('payment_plugnpay_ach_password');
		$data['payment_plugnpay_ach_authtype'] = $this->config->get('payment_plugnpay_ach_authtype') ?: 'authonly';
		$data['payment_plugnpay_ach_total'] = $this->config->get('payment_plugnpay_ach_total');
		$data['payment_plugnpay_ach_debug'] = $this->config->get('payment_plugnpay_ach_debug');

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		$data['payment_plugnpay_ach_order_status_id'] = $this->defaultOrderStatusId($data['order_statuses']);

		$data['payment_plugnpay_ach_geo_zone_id'] = $this->config->get('payment_plugnpay_ach_geo_zone_id');

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$data['payment_plugnpay_ach_status'] = $this->config->get('payment_plugnpay_ach_status');
		$data['payment_plugnpay_ach_sort_order'] = $this->config->get('payment_plugnpay_ach_sort_order');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/plugnpay_api_ach/payment/plugnpay_ach', $data));
	}

	/**
	 * Save
	 *
	 * @return void
	 */
	public function save(): void {
		$this->load->language('extension/plugnpay_api_ach/payment/plugnpay_ach');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/plugnpay_api_ach/payment/plugnpay_ach')) {
			$json['error']['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->post['payment_plugnpay_ach_merchant'])) {
			$json['error']['merchant'] = $this->language->get('error_merchant');
		}

		if (empty($this->request->post['payment_plugnpay_ach_password'])) {
			$json['error']['password'] = $this->language->get('error_password');
		}

		if (!$json) {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('payment_plugnpay_ach', $this->request->post);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Install — default order status to Pending.
	 *
	 * @return void
	 */
	public function install(): void {
		$this->load->model('setting/setting');
		$this->load->model('localisation/order_status');

		$this->model_setting_setting->editSetting('payment_plugnpay_ach', [
			'payment_plugnpay_ach_order_status_id' => $this->defaultOrderStatusId($this->model_localisation_order_status->getOrderStatuses())
		]);
	}

	/**
	 * @param list<array<string, mixed>> $order_statuses
	 */
	private function defaultOrderStatusId(array $order_statuses): int {
		return RemoteApi::resolveOrderStatusId(
			(int)$this->config->get('payment_plugnpay_ach_order_status_id'),
			$order_statuses,
			(int)$this->config->get('config_order_status_id')
		);
	}
}
