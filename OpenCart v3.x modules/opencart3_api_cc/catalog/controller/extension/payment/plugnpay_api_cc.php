<?php
class ControllerExtensionPaymentPlugnpayApiCc extends Controller {
	public function index() {
		$this->load->language('extension/payment/plugnpay_api_cc');

		$data['months'] = array();

		for ($i = 1; $i <= 12; $i++) {
			$data['months'][] = array(
				'text'  => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)),
				'value' => sprintf('%02d', $i)
			);
		}

		$today = getdate();

		$data['year_expire'] = array();

		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$data['year_expire'][] = array(
				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
			);
		}

		return $this->load->view('extension/payment/plugnpay_api_cc', $data);
	}

	public function send() {
		$url = 'https://pay1.plugnpay.com/payment/pnpremote.cgi';

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$data = array();

		$data['publisher-name'] = $this->config->get('payment_plugnpay_api_cc_login');
		$data['publisher-password'] = $this->config->get('payment_plugnpay_api_cc_key');
		$data['client'] = 'OpenCart3_API_CC';
		$data['mode'] = 'auth';
		$data['dontsndmail'] = 'yes';
		$data['easycart'] = '0';
		$data['card-name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
		$data['card-company'] = $order_info['payment_company'];
		$data['card-address'] = $order_info['payment_address_1'];
		$data['card-city'] = $order_info['payment_city'];
		$data['card-state'] = $order_info['payment_zone'];
		$data['card-zip'] = $order_info['payment_postcode'];
		$data['card-country'] = $order_info['payment_country'];
		$data['phone'] = $order_info['telephone'];
		$data['ipaddress'] = $this->request->server['REMOTE_ADDR'];
		$data['email'] = $order_info['email'];
		#$data['x_description'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
		$data['card-amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false);
		$data['currency'] = $this->session->data['currency'];
		$data['paymethod'] = 'credit';
		$data['authtype'] = ($this->config->get('payment_plugnpay_api_cc_method') == 'authpostauth') ? 'AUTH_CAPTURE' : 'AUTH_ONLY';
		$data['card-number'] = str_replace(' ', '', $this->request->post['cc_number']);
		$data['card-exp'] = $this->request->post['cc_expire_date_month'] . $this->request->post['cc_expire_date_year'];
		$data['card-cvv'] = $this->request->post['cc_cvv2'];
		$data['order-id'] = $this->session->data['order_id'];

		/* Customer Shipping Address Fields */
		if ($order_info['shipping_method']) {
            $data['shipinfo'] = '1';
			$data['shipname'] = $order_info['shipping_firstname'] . ' ' . $order_info['shipping_lastname'];
			$data['company'] = $order_info['shipping_company'];
			$data['address1'] = $order_info['shipping_address_1'] . ' ' . $order_info['shipping_address_2'];
			$data['city'] = $order_info['shipping_city'];
			$data['state'] = $order_info['shipping_zone'];
			$data['zip'] = $order_info['shipping_postcode'];
			$data['country'] = $order_info['shipping_country'];
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

			$this->log->write('PLUGNPAY API CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl));
		} elseif ($response) {
			$i = 1;

			$response_info = array();

			$results = explode(',', $response);

            parse_str($results[0], $pnp_response);

			if ($pnp_response['FinalStatus'] == 'success') {
				$message = '';

				if (isset($response_info['auth-code'])) {
					$message .= 'Authorization Code: ' . $response_info['auth-code'] . "\n";
				}

				if (isset($response_info['avs-code'])) {
					$message .= 'AVS Response: ' . $response_info['avs-code'] . "\n";
				}

				if (isset($response_info['orderID'])) {
					$message .= 'Transaction ID: ' . $response_info['orderID'] . "\n";
				}

				if (isset($response_info['resp-code'])) {
					$message .= 'Card Code Response: ' . $response_info['resp-code'] . "\n";
				}

				if (isset($response_info['cvvresp'])) {
					$message .= 'Cardholder Authentication Verification Response: ' . $response_info['cvvresp'] . "\n";
				}

				$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('config_order_status_id'), $message, false);

				$json['redirect'] = $this->url->link('checkout/success', '', true);
			} else {
				$json['error'] = $response_info['MErrMsg'];
			}
		} else {
			$json['error'] = 'Empty Gateway Response';

			$this->log->write('PLUGNPAY API CURL ERROR: Empty Gateway Response');
		}

		curl_close($curl);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
