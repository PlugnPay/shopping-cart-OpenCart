<?php
class ControllerPaymentPlugnpayAPI extends Controller {
	protected function index() {
		$this->language->load('payment/plugnpay_api');

		$this->data['text_credit_card'] = $this->language->get('text_credit_card');
		$this->data['text_wait'] = $this->language->get('text_wait');

		$this->data['entry_cc_owner'] = $this->language->get('entry_cc_owner');
		$this->data['entry_cc_number'] = $this->language->get('entry_cc_number');
		$this->data['entry_cc_exp'] = $this->language->get('entry_cc_exp');
		$this->data['entry_cc_cvv'] = $this->language->get('entry_cc_cvv');

		$this->data['button_confirm'] = $this->language->get('button_confirm');

		$this->data['months'] = array();

		for ($i = 1; $i <= 12; $i++) {
			$this->data['months'][] = array(
				'text'  => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)),
				'value' => sprintf('%02d', $i)
			);
		}

		$today = getdate();

		$this->data['year_exp'] = array();

		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$this->data['year_exp'][] = array(
				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
			);
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/plugnpay_api.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/plugnpay_api.tpl';
		} else {
			$this->template = 'default/template/payment/plugnpay_api.tpl';
		}

		$this->render();
	}

	public function send() {
		$url = 'https://pay1.plugnpay.com/payment/pnpremote.cgi';

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$data = array();

		$data['publisher_name'] = $this->config->get('plugnpay_api_merchant');
		$data['publisher_password'] = $this->config->get('plugnpay_api_passwrd');
		$data['mode'] = 'auth';
		$data['client'] = 'opencart_api';
                $data['convert'] = 'underscores';
		$data['card_name'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
		$data['card_company'] = html_entity_decode($order_info['payment_company'], ENT_QUOTES, 'UTF-8');
		$data['card_address1'] = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');
		$data['card_city'] = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');
		$data['card_state'] = html_entity_decode($order_info['payment_zone'], ENT_QUOTES, 'UTF-8');
		$data['card_zip'] = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');
		$data['card_country'] = html_entity_decode($order_info['payment_country'], ENT_QUOTES, 'UTF-8');
		$data['phone'] = $order_info['telephone'];
		$data['ipaddress'] = $this->request->server['REMOTE_ADDR'];
		$data['email'] = $order_info['email'];
		$data['x_description'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
		$data['card_amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false);
		$data['currency'] = $this->currency->getCode();
		$data['paymethod'] = 'credit';
		$data['authtype'] = ($this->config->get('plugnpay_api_authtype') == 'authpostauth') ? 'authpostauth' : 'authonly';
		$data['card_number'] = str_replace(' ', '', $this->request->post['cc_number']);
		$data['card_exp'] = $this->request->post['cc_exp_month'] . '/' . $this->request->post['cc_exp_year'];
		$data['card_cvv'] = $this->request->post['cc_cvv'];
		$data['order_id'] = $this->session->data['order_id'];

		/* Customer Shipping Address Fields */
		$data['shipname'] = html_entity_decode($order_info['shipping_firstname'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($order_info['shipping_lastname'], ENT_QUOTES, 'UTF-8');
		$data['company'] = html_entity_decode($order_info['shipping_company'], ENT_QUOTES, 'UTF-8');
		$data['address1'] = html_entity_decode($order_info['shipping_address_1'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($order_info['shipping_address_2'], ENT_QUOTES, 'UTF-8');
		$data['city'] = html_entity_decode($order_info['shipping_city'], ENT_QUOTES, 'UTF-8');
		$data['state'] = html_entity_decode($order_info['shipping_zone'], ENT_QUOTES, 'UTF-8');
		$data['zip'] = html_entity_decode($order_info['shipping_postcode'], ENT_QUOTES, 'UTF-8');
		$data['country'] = html_entity_decode($order_info['shipping_country'], ENT_QUOTES, 'UTF-8');

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

			$this->log->write('PlugnPay API CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl));
		} elseif ($response) {
			$i = 1;

			$response_info = array();

			$results = explode(',', $response);

			foreach ($results as $result) {
				$response_info[$i] = trim($result, '"');

				$i++;
			}

			# NOTE: windows server users, you must have 'register_globals' ON in your php.ini for parse_str to work correctly.
			parse_str($response_info[1]);

			if ($FinalStatus == 'success') {
				$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'));

				$message = '';

				if (isset($auth_code)) {
					$message .= 'Authorization Code: ' . $auth_code . "\n";
				}

				if (isset($avs_code)) {
					$message .= 'AVS Response: ' . $avs_code . "\n";
				}

				if (isset($orderID)) {
					$message .= 'Transaction ID: ' . $orderID . "\n";
				}

				if (isset($cvvresp)) {
					$message .= 'Card Code Response: ' . $cvvresp . "\n";
				}

				if (isset($resp_code)) {
					$message .= 'Cardholder Authentication Verification Response: ' . $resp_code . "\n";
				}

				$this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('plugnpay_api_order_status_id'), $message, false);

				$json['success'] = $this->url->link('checkout/success', '', 'SSL');
			} else {
				$json['error'] = 'FinalStatus:' . $FinalStatus . ' MErrMsg:' . $MErrMsg . ' ' . $response_info[4];
			}
		} else {
			$json['error'] = 'Empty Gateway Response';

			$this->log->write('PlugnPay API CURL ERROR: Empty Gateway Response');
		}

		curl_close($curl);

		$this->response->setOutput(json_encode($json));
	}
}
?>
