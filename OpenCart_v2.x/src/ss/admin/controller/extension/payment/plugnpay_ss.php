<?php
class ControllerExtensionPaymentPlugnPaySs extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/payment/plugnpay_ss');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('plugnpay_ss', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');

		$data['entry_merchant'] = $this->language->get('entry_merchant');
		$data['entry_authkey'] = $this->language->get('entry_authkey');
		$data['entry_callback'] = $this->language->get('entry_callback');
		$data['entry_respkey'] = $this->language->get('entry_respkey');
		$data['entry_postauth'] = $this->language->get('entry_postauth');
		$data['entry_total'] = $this->language->get('entry_total');
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

                $data['help_authkey'] = $this->language->get('help_authkey');
		$data['help_callback'] = $this->language->get('help_callback');
		$data['help_respkey'] = $this->language->get('help_respkey');
		$data['help_total'] = $this->language->get('help_total');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['merchant'])) {
			$data['error_merchant'] = $this->error['merchant'];
		} else {
			$data['error_merchant'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/plugnpay_ss', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/plugnpay_ss', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true);

		if (isset($this->request->post['plugnpay_ss_merchant'])) {
			$data['plugnpay_ss_merchant'] = $this->request->post['plugnpay_ss_merchant'];
		} else {
			$data['plugnpay_ss_merchant'] = $this->config->get('plugnpay_ss_merchant');
		}

		if (isset($this->request->post['plugnpay_ss_authkey'])) {
			$data['plugnpay_ss_authkey'] = $this->request->post['plugnpay_ss_authkey'];
		} else {
			$data['plugnpay_ss_authkey'] = $this->config->get('plugnpay_ss_authkey');
		}

		if (isset($this->request->post['plugnpay_ss_postauth'])) {
			$data['plugnpay_ss_postauth'] = $this->request->post['plugnpay_ss_postauth'];
		} else {
			$data['plugnpay_ss_postauth'] = $this->config->get('plugnpay_ss_postauth');
		}

		$data['plugnpay_ss_callback'] = HTTP_CATALOG . 'index.php?route=extension/payment/plugnpay_ss/callback';

		if (isset($this->request->post['plugnpay_ss_respkey'])) {
			$data['plugnpay_ss_respkey'] = $this->request->post['plugnpay_ss_respkey'];
		} else {
			$data['plugnpay_ss_respkey'] = $this->config->get('plugnpay_ss_respkey');
		}

		if (isset($this->request->post['plugnpay_ss_total'])) {
			$data['plugnpay_ss_total'] = $this->request->post['plugnpay_ss_total'];
		} else {
			$data['plugnpay_ss_total'] = $this->config->get('plugnpay_ss_total');
		}

		if (isset($this->request->post['plugnpay_ss_order_status_id'])) {
			$data['plugnpay_ss_order_status_id'] = $this->request->post['plugnpay_ss_order_status_id'];
		} else {
			$data['plugnpay_ss_order_status_id'] = $this->config->get('plugnpay_ss_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['plugnpay_ss_geo_zone_id'])) {
			$data['plugnpay_ss_geo_zone_id'] = $this->request->post['plugnpay_ss_geo_zone_id'];
		} else {
			$data['plugnpay_ss_geo_zone_id'] = $this->config->get('plugnpay_ss_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['plugnpay_ss_status'])) {
			$data['plugnpay_ss_status'] = $this->request->post['plugnpay_ss_status'];
		} else {
			$data['plugnpay_ss_status'] = $this->config->get('plugnpay_ss_status');
		}

		if (isset($this->request->post['plugnpay_ss_sort_order'])) {
			$data['plugnpay_ss_sort_order'] = $this->request->post['plugnpay_ss_sort_order'];
		} else {
			$data['plugnpay_ss_sort_order'] = $this->config->get('plugnpay_ss_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/plugnpay_ss', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/plugnpay_ss')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['plugnpay_ss_merchant']) {
			$this->error['merchant'] = $this->language->get('error_merchant');
		}

		return !$this->error;
	}
}
