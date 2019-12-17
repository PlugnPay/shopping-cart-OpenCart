<?php 
class ControllerPaymentPlugnpayAPIACH extends Controller {
	private $error = array();

	public function index() {
		$this->language->load('payment/plugnpay_api_ach');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('plugnpay_api_ach', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_authonly'] = $this->language->get('text_authonly');
		$this->data['text_authpostauth'] = $this->language->get('text_authpostauth');

		$this->data['entry_merchant'] = $this->language->get('entry_merchant');
		$this->data['entry_passwrd'] = $this->language->get('entry_passwrd');
		$this->data['entry_authtype'] = $this->language->get('entry_authtype');
		$this->data['entry_total'] = $this->language->get('entry_total');
		$this->data['entry_order_status'] = $this->language->get('entry_order_status');
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['merchant'])) {
			$this->data['error_merchant'] = $this->error['merchant'];
		} else {
			$this->data['error_merchant'] = '';
		}

		if (isset($this->error['passwrd'])) {
			$this->data['error_passwrd'] = $this->error['passwrd'];
		} else {
			$this->data['error_passwrd'] = '';
		}

		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/plugnpay_api_ach', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['action'] = $this->url->link('payment/plugnpay_api_ach', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['plugnpay_api_ach_merchant'])) {
			$this->data['plugnpay_api_ach_merchant'] = $this->request->post['plugnpay_api_ach_merchant'];
		} else {
			$this->data['plugnpay_api_ach_merchant'] = $this->config->get('plugnpay_api_ach_merchant');
		}

		if (isset($this->request->post['plugnpay_api_ach_passwrd'])) {
			$this->data['plugnpay_api_ach_passwrd'] = $this->request->post['plugnpay_api_ach_passwrd'];
		} else {
			$this->data['plugnpay_api_ach_passwrd'] = $this->config->get('plugnpay_api_ach_passwrd');
		}

		if (isset($this->request->post['plugnpay_api_ach_authtype'])) {
			$this->data['plugnpay_api_ach_authtype'] = $this->request->post['plugnpay_api_ach_authtype'];
		} else {
			$this->data['plugnpay_api_ach_authtype'] = $this->config->get('plugnpay_api_ach_authtype');
		}

		if (isset($this->request->post['plugnpay_api_ach_total'])) {
			$this->data['plugnpay_api_ach_total'] = $this->request->post['plugnpay_api_ach_total'];
		} else {
			$this->data['plugnpay_api_ach_total'] = $this->config->get('plugnpay_api_ach_total');
		}

		if (isset($this->request->post['plugnpay_api_ach_order_status_id'])) {
			$this->data['plugnpay_api_ach_order_status_id'] = $this->request->post['plugnpay_api_ach_order_status_id'];
		} else {
			$this->data['plugnpay_api_ach_order_status_id'] = $this->config->get('plugnpay_api_ach_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['plugnpay_api_ach_geo_zone_id'])) {
			$this->data['plugnpay_api_ach_geo_zone_id'] = $this->request->post['plugnpay_api_ach_geo_zone_id'];
		} else {
			$this->data['plugnpay_api_ach_geo_zone_id'] = $this->config->get('plugnpay_api_ach_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['plugnpay_api_ach_status'])) {
			$this->data['plugnpay_api_ach_status'] = $this->request->post['plugnpay_api_ach_status'];
		} else {
			$this->data['plugnpay_api_ach_status'] = $this->config->get('plugnpay_api_ach_status');
		}

		if (isset($this->request->post['plugnpay_api_ach_sort_order'])) {
			$this->data['plugnpay_api_ach_sort_order'] = $this->request->post['plugnpay_api_ach_sort_order'];
		} else {
			$this->data['plugnpay_api_ach_sort_order'] = $this->config->get('plugnpay_api_ach_sort_order');
		}

		$this->template = 'payment/plugnpay_api_ach.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/plugnpay_api_ach')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['plugnpay_api_ach_merchant']) {
			$this->error['merchant'] = $this->language->get('error_merchant');
		}

		if (!$this->request->post['plugnpay_api_ach_passwrd']) {
			$this->error['passwrd'] = $this->language->get('error_password');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>
