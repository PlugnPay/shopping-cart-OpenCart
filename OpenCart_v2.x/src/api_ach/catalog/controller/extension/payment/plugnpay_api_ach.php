<?php
class ControllerExtensionPaymentPlugnPayApiAch extends Controller {
	public function index() {
		$this->load->language('extension/payment/plugnpay_api_ach');

		$data['text_ach_echeck'] = $this->language->get('text_ach_echeck');
		$data['text_wait'] = $this->language->get('text_wait');

		$data['entry_accttype'] = $this->language->get('entry_accttype');
		$data['entry_accttype_checking'] = $this->language->get('entry_accttype_checking');
		$data['entry_accttype_savings'] = $this->language->get('entry_accttype_savings');

		$data['entry_acctclass'] = $this->language->get('entry_acctclass');
		$data['entry_acctclass_personal'] = $this->language->get('entry_acctclass_personal');
		$data['entry_acctclass_business'] = $this->language->get('entry_acctclass_business');

		$data['entry_owner'] = $this->language->get('entry_owner');
		$data['entry_routingnum'] = $this->language->get('entry_routingnum');
		$data['entry_accountnum'] = $this->language->get('entry_accountnum');
		$data['entry_checknum'] = $this->language->get('entry_checknum');

		$data['error_owner'] = $this->language->get('error_owner');
		$data['error_accttype'] = $this->language->get('error_accttype');
		$data['error_acctclass'] = $this->language->get('error_acctclass');
		$data['error_routingnum'] = $this->language->get('error_routingnum');
		$data['error_accountnum'] = $this->language->get('error_accountnum');
		$data['error_checknum'] = $this->language->get('error_checknum');

		$data['button_confirm'] = $this->language->get('button_confirm');

		return $this->load->view('extension/payment/plugnpay_api_ach', $data);
	}

	public function send() {
		$url = 'https://pay1.plugnpay.com/payment/pnpremote.cgi';

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$data = array();

		// fast hack to do some ACH/eCheck data validation
		$error = $this->validate();
		if ($error) {
			$json = array();
			$json['error'] = $error;
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}

		$data['publisher-name'] = $this->config->get('plugnpay_api_ach_login');
		$data['publisher-password'] = $this->config->get('plugnpay_api_ach_key');
		$data['client'] = 'OpenCart2 API ACH';
		$data['card-name'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
		$data['card-company'] = html_entity_decode($order_info['payment_company'], ENT_QUOTES, 'UTF-8');
		$data['card-address1'] = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');
		$data['card-address2'] = html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8');
		$data['card-city'] = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');
		if ($order_info['payment_iso_code_2'] == 'US') {
			$data['card-state'] = html_entity_decode($order_info['payment_zone_code'], ENT_QUOTES, 'UTF-8');
			$data['card-province'] = '';
		}
		else {
			$data['card-state'] = '';
			$data['card-province'] = html_entity_decode($order_info['payment_zone'], ENT_QUOTES, 'UTF-8');
		}
		$data['card-zip'] = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');
		$data['card-country'] = html_entity_decode($order_info['payment_country'], ENT_QUOTES, 'UTF-8');
		$data['phone'] = $order_info['telephone'];
		$data['ipaddress'] = $this->request->server['REMOTE_ADDR'];
		$data['email'] = $order_info['email'];
		$data['comments'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
		$data['card-amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false);
		$data['currency'] = $this->session->data['currency'];
		$data['paymethod'] = 'onlinecheck';
		$data['checktype'] = 'WEB';
		$data['accttype'] = $this->request->post['ach_accttype'];
		$data['routingnum'] = preg_replace('/[^0-9]/', '', $this->request->post['ach_routingnum']);
		$data['accountnum'] = preg_replace('/[^0-9]/', '', $this->request->post['ach_accountnum']);
		$data['checknum'] = preg_replace('/[^0-9]/', '', $this->request->post['ach_checknum']);
		if ($this->request->post['ach_acctclass'] == 'business') {
			$data['acctclass'] = 'business';
			$data['commcardtype'] = 'business';
		}
		else {
			$data['acctclass'] = 'personal';
			$data['commcardtype'] = '';
		}
		$data['authtype'] = ($this->config->get('plugnpay_api_ach_method') == 'authpostauth') ? 'AUTH_CAPTURE' : 'AUTH_ONLY';
		$data['order-id'] = $this->session->data['order_id'];

		/* Customer Shipping Address Fields */
		if ($order_info['shipping_method']) {
			$data['shipname'] = html_entity_decode($order_info['shipping_firstname'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($order_info['shipping_lastname'], ENT_QUOTES, 'UTF-8');
			$data['company'] = html_entity_decode($order_info['shipping_company'], ENT_QUOTES, 'UTF-8');
			$data['address1'] = html_entity_decode($order_info['shipping_address_1'], ENT_QUOTES, 'UTF-8');
			$data['address2'] = html_entity_decode($order_info['shipping_address_1'], ENT_QUOTES, 'UTF-8');
			$data['city'] = html_entity_decode($order_info['shipping_city'], ENT_QUOTES, 'UTF-8');
			if ($order_info['shipping_iso_code_2'] == 'US') {
				$data['state'] = html_entity_decode($order_info['shipping_zone_code'], ENT_QUOTES, 'UTF-8');
				$data['province'] = '';
			}
			else {
				$data['state'] = '';
				$data['province'] = html_entity_decode($order_info['shipping_zone'], ENT_QUOTES, 'UTF-8');
			}
			$data['zip'] = html_entity_decode($order_info['shipping_postcode'], ENT_QUOTES, 'UTF-8');
			$data['country'] = html_entity_decode($order_info['shipping_country'], ENT_QUOTES, 'UTF-8');
		}

		$curl = curl_init($url);

		curl_setopt($curl, CURLOPT_PORT, 443);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));

		$response = curl_exec($curl);

		$json = array();

		if (curl_error($curl)) {
			$json['error'] = 'CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl);

			$this->log->write('PLUGNPAY API ACH CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl));
		} elseif ($response) {

			$response_info = array();

			$results = explode(',', $response);

			parse_str($results[0], $pnp_response);

			if ($pnp_response['FinalStatus'] == 'success') {
				$message = '';

				if (isset($pnp_response['auth-code'])) {
					$message .= 'Authorization Code: ' . $pnp_response['auth-code'] . "\n";
				}

				if (isset($pnp_response['avs-code'])) {
					$message .= 'AVS Response: ' . $pnp_response['avs-code'] . "\n";
				}

				if (isset($pnp_response['orderID'])) {
					$message .= 'Transaction ID: ' . $pnp_response['orderID'] . "\n";
				}

				if (isset($pnp_response['resp-code'])) {
					$message .= 'Response Code: ' . $pnp_response['resp-code'] . "\n";
				}

				$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('plugnpay_api_ach_order_status_id'), $message, false);

				$json['redirect'] = $this->url->link('checkout/success', '', true);
			} else {
				$json['error'] = $pnp_response['MErrMsg'];
			}
		} else {
			$json['error'] = 'Empty Gateway Response';

			$this->log->write('PLUGNPAY API ACH CURL ERROR: Empty Gateway Response');
		}

		curl_close($curl);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function validate() {
		$this->load->language('extension/payment/plugnpay_api_ach');

		$this->load->model('extension/payment/plugnpay_api_ach');

		$error = '';

		if ((!isset($this->request->post['ach_owner'])) || ($this->request->post['ach_owner'] == '')) {
			$error .= $this->language->get('error_owner') . "\n";
		}
		else if ((!isset($this->request->post['ach_accttype'])) || ($this->request->post['ach_accttype'] == '')) {
			$error .= $this->language->get('error_accttype') . "\n";
		}
		else if ((!isset($this->request->post['ach_acctclass'])) || ($this->request->post['ach_acctclass'] == '')) {
			$error .= $this->language->get('error_acctclass') . "\n";
		}
		else if (!isset($this->request->post['ach_routingnum']) || utf8_strlen($this->request->post['ach_routingnum']) < 1 || utf8_strlen($this->request->post['ach_routingnum']) > 9) {
			$error .= $this->language->get('error_routingnum') . "\n";
		}
		else if (!isset($this->request->post['ach_accountnum']) || utf8_strlen($this->request->post['ach_accountnum']) < 1 || utf8_strlen($this->request->post['ach_accountnum']) > 19) {
			$error .= $this->language->get('error_accountnum') . "\n";
		}
		else if (!isset($this->request->post['ach_checknum']) || utf8_strlen($this->request->post['ach_checknum']) < 1 || utf8_strlen($this->request->post['ach_checknum']) > 19) { 
			$error .= $this->language->get('error_checknum') . "\n";
		}

		return $error;
	}

}
