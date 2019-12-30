<?php
class ControllerExtensionPaymentPlugnpaySs2 extends Controller {
	public function index() {
		$this->load->language('extension/payment/plugnpay_ss2');

		$data['button_confirm'] = $this->language->get('button_confirm');

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$data['pt_gateway_account'] = $this->config->get('payment_plugnpay_ss2_merchant');
		$data['pt_order_classifier'] = $this->session->data['order_id'];
		$data['pt_transaction_amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$data['pd_display_items'] = 'no';
		$data['pt_transaction_type'] = $this->config->get('payment_plugnpay_ss2_mode');
		$data['pb_post_auth'] = 'yes';
		$data['pt_currency'] = $this->session->data['currency'];
		$data['pt_payment_name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
		$data['pt_billing_company'] = $order_info['payment_company'];
		$data['pt_billing_address_1'] = $order_info['payment_address_1'] . ' ' . $order_info['payment_address_2'];
		$data['pt_billing_city'] = $order_info['payment_city'];
		$data['pt_billing_state'] = $order_info['payment_zone'];
		$data['pt_billing_postal_code'] = $order_info['payment_postcode'];
		$data['pt_billing_country'] = $order_info['payment_country'];
		$data['pt_billing_phone_number'] = $order_info['telephone'];
		$data['pt_billing_email_address'] = $order_info['email'];
		$data['pb_transition_type'] = 'hidden';
		$data['pb_success_url'] = $this->config->get('payment_plugnpay_ss2_callback');

		return $this->load->view('extension/payment/plugnpay_ss2', $data);
	}

	public function callback() {
		if ($this->request->post['pi_response_status'] != '') {
			$this->load->model('checkout/order');

			$order_info = $this->model_checkout_order->getOrder($this->request->post['pt_order_classifier']);

			if ($order_info && $this->request->post['pi_response_status'] == 'success') {
				$message = '';

				if (isset($this->request->post['pi_error_message'])) {
					$message .= 'Response Text: ' . $this->request->post['pi_error_message'] . "\n";
				}

				if (isset($this->request->post['pi_response_code'])) {
					$message .= 'Response Code: ' . $this->request->post['pi_response_code'] . "\n";
				}

				if (isset($this->request->post['pt_authorization_code'])) {
					$message .= 'Authorization Code: ' . $this->request->post['pt_authorization_code'];
				}

				$this->model_checkout_order->addOrderHistory($this->request->post['pt_order_classifier'], $this->config->get('payment_plugnpay_ss2_order_status_id'), $message, true);

				$this->response->redirect($this->url->link('checkout/success'));
			} else {
				$this->response->redirect($this->url->link('checkout/failure'));
			}
		} else {
			$this->response->redirect($this->url->link('checkout/failure'));
		}
	}
}
