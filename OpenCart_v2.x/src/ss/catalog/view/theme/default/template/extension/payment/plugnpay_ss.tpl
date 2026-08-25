<form action="https://pay1.plugnpay.com/pay/" method="post">
  <input type="hidden" name="pt_gateway_account" value="<?php echo $pt_gateway_account; ?>" />
  <input type="hidden" name="pt_order_classifier" value="<?php echo $pt_order_classifier; ?>" />
  <input type="hidden" name="pt_transaction_time" value="<?php echo $pt_transaction_time; ?>" />
  <input type="hidden" name="pt_transaction_amount" value="<?php echo $pt_transaction_amount; ?>" />
  <input type="hidden" name="pt_transaction_hash" value="<?php echo $pt_transaction_hash; ?>" />
  <input type="hidden" name="pt_client_identifier" value="OpenCart SS" />
  <input type="hidden" name="pt_currency" value="<?php echo $pt_currency; ?>" />
  <input type="hidden" name="pt_account_code_1" value="<?php echo $pt_account_code_1; ?>" />
  <input type="hidden" name="pt_billing_first_name" value="<?php echo $pt_billing_first_name; ?>" />
  <input type="hidden" name="pt_billing_last_name" value="<?php echo $pt_billing_last_name; ?>" />
  <input type="hidden" name="pt_billing_company" value="<?php echo $pt_billing_company; ?>" />
  <input type="hidden" name="pt_billing_address_1" value="<?php echo $pt_billing_address_1; ?>" />
  <input type="hidden" name="pt_billing_address_2" value="<?php echo $pt_billing_address_2; ?>" />
  <input type="hidden" name="pt_billing_city" value="<?php echo $pt_billing_city; ?>" />
  <input type="hidden" name="pt_billing_state" value="<?php echo $pt_billing_state; ?>" />
  <input type="hidden" name="pt_billing_province" value="<?php echo $pt_billing_province; ?>" />
  <input type="hidden" name="pt_billing_postal_code" value="<?php echo $pt_billing_postal_code; ?>" />
  <input type="hidden" name="pt_billing_country" value="<?php echo $pt_billing_country; ?>" />
  <input type="hidden" name="pt_billing_phone_number" value="<?php echo $pt_billing_phone_number; ?>" />
  <input type="hidden" name="pt_shipping_first_name" value="<?php echo $pt_shipping_first_name; ?>" />
  <input type="hidden" name="pt_shipping_last_name" value="<?php echo $pt_shipping_last_name; ?>" />
  <input type="hidden" name="pt_shipping_company" value="<?php echo $pt_shipping_company; ?>" />
  <input type="hidden" name="pt_shipping_address_1" value="<?php echo $pt_shipping_address_1; ?>" />
  <input type="hidden" name="pt_shipping_address_2" value="<?php echo $pt_shipping_address_2; ?>" />
  <input type="hidden" name="pt_shipping_city" value="<?php echo $pt_shipping_city; ?>" />
  <input type="hidden" name="pt_shipping_state" value="<?php echo $pt_shipping_state; ?>" />
  <input type="hidden" name="pt_shipping_province" value="<?php echo $pt_shipping_province; ?>" />
  <input type="hidden" name="pt_shipping_postal_code" value="<?php echo $pt_shipping_postal_code; ?>" />
  <input type="hidden" name="pt_shipping_country" value="<?php echo $pt_shipping_country; ?>" />
  <input type="hidden" name="pt_ip_address" value="<?php echo $pt_ip_address; ?>" />
  <input type="hidden" name="pt_billing_email_address" value="<?php echo $pt_billing_email_address; ?>" />
  <input type="hidden" name="pb_response_message_type" value="querystring" />
  <input type="hidden" name="pb_response_parse_type" value="querystring" />
  <input type="hidden" name="pb_success_url" value="<?php echo $pb_success_url; ?>" />
  <input type="hidden" name="pb_transition_type" value="<?php echo $pb_transition_type; ?>" />
  <input type="hidden" name="pb_post_auth" value="<?php echo $pb_post_auth; ?>" />
  <div class="buttons">
    <div class="pull-right">
      <input type="submit" value="<?php echo $button_confirm; ?>" class="btn btn-primary" />
    </div>
  </div>
</form>
