<?php
class ControllerExtensionPaymentPlugnPayApiCc extends Controller {
  public function index() {
    $this->load->language('extension/payment/plugnpay_api_cc');

    $data['text_credit_card'] = $this->language->get('text_credit_card');
    $data['text_wait'] = $this->language->get('text_wait');

    $data['entry_cc_owner'] = $this->language->get('entry_cc_owner');
    $data['entry_cc_number'] = $this->language->get('entry_cc_number');
    $data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');
    $data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');

    $data['button_confirm'] = $this->language->get('button_confirm');

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

    $data['publisher-name'] = $this->config->get('plugnpay_api_cc_login');
    $data['publisher-password'] = $this->config->get('plugnpay_api_cc_key');
    $data['client'] = 'OpenCart2 API';
    $data['card-name'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
    $data['card-company'] = html_entity_decode($order_info['payment_company'], ENT_QUOTES, 'UTF-8');
    $data['card-address1'] = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');
    $data['card-address2'] = html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8');
    $data['card-city'] = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');
    $data['card-state'] = html_entity_decode($order_info['payment_zone'], ENT_QUOTES, 'UTF-8');
    $data['card-zip'] = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');
    $data['card-country'] = html_entity_decode($order_info['payment_country'], ENT_QUOTES, 'UTF-8');
    $data['phone'] = $order_info['telephone'];
    $data['ipaddress'] = $this->request->server['REMOTE_ADDR'];
    $data['email'] = $order_info['email'];
    $data['comments'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
    $data['card-amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false);
    $data['currency'] = $this->session->data['currency'];
    $data['paymethod'] = 'credit';
    $data['mode'] = 'auth';
    $data['authtype'] = ($this->config->get('plugnpay_api_cc_method') == 'authpostauth') ? 'AUTH_CAPTURE' : 'AUTH_ONLY';
    $data['card-number'] = str_replace(' ', '', $this->request->post['cc_number']);
    $data['card-exp'] = $this->request->post['cc_expire_date_month'] . '/' . $this->request->post['cc_expire_date_year'];
    $data['card-cvv'] = $this->request->post['cc_cvv2'];
    $data['order-id'] = $this->session->data['order_id'];

    /* Customer Shipping Address Fields */
    if ($order_info['shipping_method']) {
      $data['shipname'] = html_entity_decode($order_info['shipping_firstname'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($order_info['shipping_lastname'], ENT_QUOTES, 'UTF-8');
      $data['company'] = html_entity_decode($order_info['shipping_company'], ENT_QUOTES, 'UTF-8');
      $data['address1'] = html_entity_decode($order_info['shipping_address_1'], ENT_QUOTES, 'UTF-8');
      $data['address2'] = html_entity_decode($order_info['shipping_address_1'], ENT_QUOTES, 'UTF-8');
      $data['city'] = html_entity_decode($order_info['shipping_city'], ENT_QUOTES, 'UTF-8');
      $data['state'] = html_entity_decode($order_info['shipping_zone'], ENT_QUOTES, 'UTF-8');
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

      $this->log->write('PLUGNPAY API CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl));
    } elseif ($response) {
      $i = 1;

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
          $message .= 'Card Code Response: ' . $pnp_response['resp-code'] . "\n";
        }

        if (isset($pnp_response['cvv-resp'])) {
          $message .= 'Cardholder Authentication Verification Response: ' . $pnp_response['cvv-resp'] . "\n";
        }

        $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('plugnpay_api_cc_order_status_id'), $message, false);

        $json['redirect'] = $this->url->link('checkout/success', '', true);
      } else {
        $json['error'] = $pnp_response['MErrMsg'];
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
