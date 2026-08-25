<?php
class ControllerExtensionPaymentPlugnPaySs extends Controller {
	public function index() {
		$this->load->language('extension/payment/plugnpay_ss');

		$data['button_confirm'] = $this->language->get('button_confirm');

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$data['pt_gateway_account'] = $this->config->get('plugnpay_ss_merchant');
		$data['pt_order_classifier'] = $this->session->data['order_id'];
		$data['pt_transaction_time'] = time();
		$data['pt_transaction_amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$data['pt_transaction_hash'] = null; // calculated later, once all fields are populated
		$data['pt_client_identifier'] = 'OpenCart SS';
		$data['pt_currency'] = $this->session->data['currency'];
		$data['pt_account_code_1'] = $this->session->data['order_id'];
		$data['pt_billing_first_name'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
		$data['pt_billing_last_name'] = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
		$data['pt_billing_company'] = html_entity_decode($order_info['payment_company'], ENT_QUOTES, 'UTF-8');
		$data['pt_billing_address_1'] = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');
		$data['pt_billing_address_2'] = html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8');
		$data['pt_billing_city'] = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');
		if ($order_info['payment_iso_code_2'] == 'US') {
			$data['pt_billing_state'] = html_entity_decode($order_info['payment_zone_code'], ENT_QUOTES, 'UTF-8');
			$data['pt_billing_province'] = '';
		}
		else {
			$data['pt_billing_state'] = '';
			$data['pt_billing_province'] = html_entity_decode($order_info['payment_zone'], ENT_QUOTES, 'UTF-8');
		}
		$data['pt_billing_postal_code'] = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');
		$data['pt_billing_country'] = html_entity_decode($order_info['payment_iso_code_2'], ENT_QUOTES, 'UTF-8');
		$data['pt_billing_phone_number'] = $order_info['telephone'];
		$data['pt_shipping_first_name'] = html_entity_decode($order_info['shipping_firstname'], ENT_QUOTES, 'UTF-8');
		$data['pt_shipping_last_name'] = html_entity_decode($order_info['shipping_lastname'], ENT_QUOTES, 'UTF-8');
		$data['pt_shipping_company'] = html_entity_decode($order_info['shipping_company'], ENT_QUOTES, 'UTF-8');
		$data['pt_shipping_address_1'] = html_entity_decode($order_info['shipping_address_1'], ENT_QUOTES, 'UTF-8');
		$data['pt_shipping_address_2'] = html_entity_decode($order_info['shipping_address_2'], ENT_QUOTES, 'UTF-8');
		$data['pt_shipping_city'] = html_entity_decode($order_info['shipping_city'], ENT_QUOTES, 'UTF-8');
		if ($order_info['shipping_iso_code_2'] == 'US') {
			$data['pt_shipping_state'] = html_entity_decode($order_info['shipping_zone_code'], ENT_QUOTES, 'UTF-8');
			$data['pt_shipping_province'] = '';
		}
		else {
			$data['pt_shipping_state'] = '';
			$data['pt_shipping_province'] = html_entity_decode($order_info['shipping_zone'], ENT_QUOTES, 'UTF-8');
		}
		$data['pt_shipping_postal_code'] = html_entity_decode($order_info['shipping_postcode'], ENT_QUOTES, 'UTF-8');
		$data['pt_shipping_country'] = html_entity_decode($order_info['shipping_iso_code_2'], ENT_QUOTES, 'UTF-8');
		$data['pt_ip_address'] = $this->request->server['REMOTE_ADDR'];
		$data['pt_billing_email_address'] = $order_info['email'];
		$data['pb_response_message_type'] = 'querystring';
		$data['pb_response_parse_type'] = 'querystring';
		$data['pb_success_url'] = $this->config->get('plugnpay_ss_callback');
		$data['pb_transition_type'] = 'post';

		if ($this->config->get('plugnpay_ss_postauth')) {  
			$data['pb_post_auth'] = 'yes';
		}
		else {
			$data['pb_post_auth'] = 'no';
		}

		if ($this->config->get('plugnpay_ss_authhash') == 1) {
			$data['pt_transaction_hash'] = hash_hmac('md5', $this->config->get('plugnpay_ss_authkey') . $data['pt_transaction_time'] . $data['pt_gateway_account'] . $data['pt_order_classifier'] . $data['pt_transaction_amount'], '');
		}

		return $this->load->view('extension/payment/plugnpay_ss', $data);
	}

	public function callback() {
		if ((!$this->config->get('plugnpay_ss_respkey')) || (md5($this->config->get('plugnpay_ss_respkey') . $this->request->post['pt_gateway_account'] . $this->request->post['pt_order_id'] . $this->request->post['pt_transaction_amount']) == strtolower($this->request->post['pt_transaction_response_hash']))) {

			$this->load->model('checkout/order');

			$order_info = $this->model_checkout_order->getOrder($this->request->post['pt_order_classifier']);

			if ($order_info && $this->request->post['pi_response_status'] == 'success') {
				$comment = '';
				if (isset($this->request->post['pt_order_id'])) {
					$comment .= 'Payment OrderID: ' . $this->request->post['pt_order_id'] . "\n";
				}
				if (isset($this->request->post['pt_authorization_code'])) {
					$comment .= 'Payment AuthCode: ' . $this->request->post['pt_authorization_code'] . "\n";
				}

				$this->model_checkout_order->addOrderHistory($this->request->post['pt_order_classifier'], $this->config->get('plugnpay_ss_order_status_id'), $comment, true);

				$this->response->redirect($this->url->link('checkout/success'));
			} else {
				$this->response->redirect($this->url->link('checkout/failure'));
			}
		} else {
			$this->response->redirect($this->url->link('checkout/failure'));
		}
	}
}
