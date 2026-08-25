<?php
// Heading
$_['heading_title']      = 'PlugnPay Remote API (ACH / eCheck)';

// Text
$_['text_extension']     = 'Extensions';
$_['text_success']       = 'Success: You have modified PlugnPay Remote API (ACH / eCheck)!';
$_['text_edit']          = 'Edit PlugnPay Remote API (ACH / eCheck)';
$_['text_authonly']      = 'Authorization only (authonly)';
$_['text_authpostauth']  = 'Authorize and settle (authpostauth)';

// Entry
$_['entry_merchant']     = 'Gateway Account';
$_['entry_password']     = 'Remote Client Password';
$_['entry_authtype']     = 'Authorization Type';
$_['entry_total']        = 'Total';
$_['entry_order_status'] = 'Order Status';
$_['entry_geo_zone']     = 'Geo Zone';
$_['entry_status']       = 'Status';
$_['entry_sort_order']   = 'Sort Order';
$_['entry_debug']        = 'Debug Logging';

// Help
$_['help_merchant']      = 'PlugnPay publisher / gateway username. ACH must be enabled on the account. There is no public demo account.';
$_['help_password']      = 'Remote Client Password from PlugnPay Security Administration — not your admin login password.';
$_['help_authtype']      = 'authonly leaves the order unpaid until you capture in PlugnPay Admin. authpostauth authorizes and settles.';
$_['help_total']         = 'The checkout total the order must reach before this payment method becomes active.';
$_['help_order_status']  = 'Use Pending for authonly. Use Processing (or paid) for authpostauth. Capture, void, and refund are done in PlugnPay Merchant Admin.';
$_['help_debug']         = 'Writes sanitized request/response logs under the OpenCart storage logs directory. Routing, account, check number, and password are never logged in full.';

// Error
$_['error_permission']   = 'Warning: You do not have permission to modify PlugnPay Remote API (ACH / eCheck)!';
$_['error_merchant']     = 'Gateway Account required!';
$_['error_password']     = 'Remote Client Password required!';
