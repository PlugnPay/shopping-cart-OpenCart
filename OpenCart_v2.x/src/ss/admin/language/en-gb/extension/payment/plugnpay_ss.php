<?php
// Heading
$_['heading_title']		= 'PlugnPay (SSv2)';

// Text
$_['text_extension']		= 'Extensions';
$_['text_success']		= 'Success: You have modified PlugnPay (SSv2) account details!';
$_['text_edit']			= 'Edit PlugnPay (SSv2)';
$_['text_plugnpay_ss']		= '<a onclick="window.open(\'http://www.plugnpay.com/\');"><img src="view/image/payment/plugnpay.png" alt="PlugnPay" title="PlugnPay" style="border: 1px solid #EEEEEE;" /></a>';

// Entry
$_['entry_merchant']		= 'PlugnPay Username';
$_['entry_authkey']		= 'Authorization Verification Hash Key';
$_['entry_callback']		= 'Callback URL';
$_['entry_respkey']		= 'Response Verification Hash Key';
$_['entry_postauth']		= 'Auto Settle Transactions';
$_['entry_total']		= 'Total';
$_['entry_order_status']	= 'Order Status';
$_['entry_geo_zone']		= 'Geo Zone';
$_['entry_status']		= 'Status';
$_['entry_sort_order']		= 'Sort Order';

// Help
$_['help_authkey']		= '(optional) The Authorization Verification Hash feature enables PlugnPay to authenticate transaction data is securely received from your cart. Please login and set this at <a href="https://pay1.plugnpay.com/admin/security.cgi?function=show_verifyhash_menu" target="_blank" class="txtLink">https://pay1.plugnpay.com</a>.(Optional) \[This module assumes "pt_gateway_account", "pt_transaction_amount", "pt_transaction_time" fields are selected.\]';
$_['help_respkey']		= '(optional) The Response Verification Hash feature enables you to authenticate that a transaction response is securely received from PlugnPay. Please login and set this at <a href="https://pay1.plugnpay.com/" target="_blank" class="txtLink">https://pay1.plugnpay.com</a>.(Optional) \[This module assumes "pt_gateway_account", "pt_order_id", "pt_transaction_amount" fields are selected.\]';
$_['help_total']		= 'The checkout total the order must reach before this payment method becomes active.';

// Error
$_['error_permission']		= 'Warning: You do not have permission to modify payment PlugnPay (SSv2)!';
$_['error_merchant']		= 'PlugnPay Username Required!';
