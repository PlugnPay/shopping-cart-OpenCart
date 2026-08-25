<?php
/**
 * CLI checks for RemoteApi field mapping, response parse, and RemoteApiLogger.
 *
 * Run: php OpenCart_v4.1.x/tests/test_api_protocol.php
 */

declare(strict_types=1);

$base = dirname(__DIR__) . '/src/api_cc/system/library';
require_once $base . '/remote_api.php';
require_once $base . '/remote_api_logger.php';

use Opencart\System\Library\Extension\PlugnpayApiCc\RemoteApi;
use Opencart\System\Library\Extension\PlugnpayApiCc\RemoteApiLogger;

$failed = 0;

function assert_true(bool $cond, string $label): void {
	global $failed;
	if ($cond) {
		echo "OK  {$label}\n";
		return;
	}
	$failed++;
	echo "FAIL {$label}\n";
}

function oc_class_to_file(string $class): string {
	return strtolower((string)preg_replace('~([a-z])([A-Z]|[0-9])~', '$1_$2', $class));
}

assert_true(oc_class_to_file('PlugnpayCc') === 'plugnpay_cc', 'PlugnpayCc maps to plugnpay_cc.php');
assert_true(oc_class_to_file('PlugnpayAch') === 'plugnpay_ach', 'PlugnpayAch maps to plugnpay_ach.php');
assert_true(oc_class_to_file('RemoteApi') === 'remote_api', 'RemoteApi maps to remote_api.php');
assert_true(oc_class_to_file('RemoteApiLogger') === 'remote_api_logger', 'RemoteApiLogger maps to remote_api_logger.php');

$fields = RemoteApi::buildCreditFields([
	'authtype'       => 'authonly',
	'amount'         => '12.34',
	'currency'       => 'USD',
	'order_id'       => '42',
	'card_number'    => '4111-1111-1111-1111',
	'card_exp'       => '01/30',
	'card_name'      => 'Jane Doe',
	'card_cvv'       => '123',
	'card_company'   => 'Acme',
	'card_address1'  => '1 Main St',
	'card_city'      => 'Portland',
	'card_state'     => 'OR',
	'card_zip'       => '97201',
	'card_country'   => 'US',
	'phone'          => '555-0100',
	'email'          => 'jane@example.com',
	'ship_name'      => 'Jane Doe',
	'ship_address1'  => '9 Ship Ln',
	'ship_city'      => 'Seattle',
	'ship_state'     => 'WA',
	'ship_zip'       => '98101',
	'ship_country'   => 'US',
	'shipping'       => '5.00',
	'tax'            => '1.00',
	'ip_address'     => '203.0.113.10',
	'items'          => [
		['sku' => 'SKU-1', 'price' => 6.17, 'quantity' => 2, 'name' => 'Widget'],
	],
]);

assert_true($fields['mode'] === 'auth', 'mode auth');
assert_true($fields['paymethod'] === 'credit', 'paymethod credit');
assert_true($fields['authtype'] === 'authonly', 'authtype authonly');
assert_true($fields['easycart'] === '1', 'easycart');
assert_true($fields['shipinfo'] === '1', 'shipinfo when shipping present');
assert_true($fields['orderID'] === '42', 'orderID');
assert_true($fields['acct_code'] === '42', 'acct_code');
assert_true($fields['card-amount'] === '12.34', 'card-amount');
assert_true($fields['card-number'] === '4111111111111111', 'card-number digits only');
assert_true($fields['card-exp'] === '01/30', 'card-exp MM/YY');
assert_true($fields['card-cvv'] === '123', 'card-cvv present');
assert_true($fields['dontsndmail'] === 'yes', 'dontsndmail');
assert_true($fields['client'] === 'OpenCart_API_CC', 'credit client identifier');
assert_true($fields['item1'] === 'SKU-1', 'line item sku');
assert_true($fields['cost1'] === '6.17', 'line item cost');
assert_true(RemoteApi::ENDPOINT === 'https://pay1.plugnpay.com/payment/pnpremote.cgi', 'endpoint');
assert_true(RemoteApi::normalizeAuthtype('capture') === 'authpostauth', 'capture maps to authpostauth');
assert_true(RemoteApi::normalizeAuthtype('AUTH_ONLY') === 'authonly', 'AUTH_ONLY maps to authonly');

$statuses = [
	['order_status_id' => 2, 'name' => 'Processing'],
	['order_status_id' => 5, 'name' => 'Pending'],
];
assert_true(RemoteApi::resolveOrderStatusId(0, $statuses, 2) === 5, 'unset status defaults to Pending');
assert_true(RemoteApi::resolveOrderStatusId(2, $statuses, 5) === 2, 'saved status is kept');

$sale = RemoteApi::buildCreditFields([
	'authtype'    => 'authpostauth',
	'amount'      => '1.00',
	'card_number' => '4111111111111111',
	'card_exp'    => '01/30',
	'card_name'   => 'Jane Doe',
	'order_id'    => 'oid',
]);
assert_true($sale['authtype'] === 'authpostauth', 'authtype sale');
assert_true(!isset($sale['card-cvv']), 'omit empty cvv');

$zero = RemoteApi::buildCreditFields([
	'authtype'    => 'authonly',
	'amount'      => '0.00',
	'card_number' => '4111111111111111',
	'card_exp'    => '01/30',
	'card_name'   => 'Jane Doe',
	'order_id'    => 'oid',
]);
assert_true($zero['mode'] === 'checkcard', 'zero amount uses checkcard');
assert_true(!isset($zero['card-amount']), 'zero amount omits card-amount');

$ach = RemoteApi::buildAchFields([
	'authtype'     => 'authonly',
	'amount'       => '25.00',
	'order_id'     => '99',
	'account_name' => 'Jane Doe',
	'accttype'     => 'savings',
	'routingnum'   => '111000025',
	'accountnum'   => '123456789',
	'checknum'     => '1001',
	'business'     => true,
	'currency'     => 'USD',
]);
assert_true($ach['paymethod'] === 'onlinecheck', 'ach paymethod');
assert_true($ach['accttype'] === 'savings', 'ach savings');
assert_true($ach['checktype'] === 'WEB', 'ach WEB checktype');
assert_true($ach['routingnum'] === '111000025', 'routing digits');
assert_true($ach['acctclass'] === 'business', 'business class');
assert_true($ach['commcardtype'] === 'business', 'business commcardtype');
assert_true($ach['client'] === 'OpenCart_API_ACH', 'ach client identifier');

$personal = RemoteApi::buildAchFields([
	'amount'       => '1.00',
	'order_id'     => '1',
	'account_name' => 'Jane Doe',
	'accttype'     => 'bogus',
	'routingnum'   => '111-000-025',
	'accountnum'   => '1234',
	'checknum'     => '1',
]);
assert_true($personal['accttype'] === 'checking', 'invalid accttype becomes checking');
assert_true($personal['routingnum'] === '111000025', 'routing strips dashes');
assert_true(!isset($personal['acctclass']), 'personal omits acctclass');

$parsed = RemoteApi::parseResponse(
	'FinalStatus=success&success=yes&orderID=PNP-99&auth-code=TST123&avs-code=Y&cvvresp=M'
);
$api = new RemoteApi('demoacct', 'not-a-real-password');
assert_true($api->isApproved($parsed), 'success FinalStatus is approved');
assert_true($parsed['orderID'] === 'PNP-99', 'parse orderID');
assert_true(!$api->isApproved(['FinalStatus' => 'badcard', 'success' => 'no']), 'decline is not approved');

$logger = new RemoteApiLogger(sys_get_temp_dir(), false);
$sanitized = $logger->sanitize([
	'card-number' => '4111111111111111',
	'card-cvv' => '123',
	'publisher-password' => 'secret',
	'routingnum' => '111000025',
	'accountnum' => '123456789',
	'checknum' => '1001',
	'publisher-name' => 'pnpdemo',
	'fields' => [
		'card-number' => '4111111111111111',
		'publisher-password' => 'secret',
	],
]);
assert_true($sanitized['card-number'] === 'XXXXXXXXXXXX1111', 'PAN last four only');
assert_true($sanitized['card-cvv'] === '***REDACTED***', 'CVV redacted');
assert_true($sanitized['publisher-password'] === '***REDACTED***', 'password redacted');
assert_true($sanitized['routingnum'] === 'XXXXX0025', 'routing last four only');
assert_true($sanitized['accountnum'] === 'XXXXX6789', 'account last four only');
assert_true($sanitized['checknum'] === '***REDACTED***', 'check number redacted');
assert_true($sanitized['publisher-name'] === 'pnpdemo', 'account not redacted');
assert_true($sanitized['fields']['card-number'] === 'XXXXXXXXXXXX1111', 'nested PAN redacted');

$raw_dump = print_r($sanitized, true);
assert_true(strpos($raw_dump, '4111111111111111') === false, 'sanitized dump has no full PAN');
assert_true(strpos($raw_dump, 'secret') === false, 'sanitized dump has no password');

$cc_install = dirname(__DIR__) . '/src/api_cc/install.json';
$ach_install = dirname(__DIR__) . '/src/api_ach/install.json';
$cc_meta = is_file($cc_install) ? json_decode((string)file_get_contents($cc_install), true) : null;
$ach_meta = is_file($ach_install) ? json_decode((string)file_get_contents($ach_install), true) : null;
assert_true(is_array($cc_meta) && ($cc_meta['version'] ?? '') === '1.0.0', 'cc install.json version');
assert_true(is_array($ach_meta) && str_contains((string)($ach_meta['name'] ?? ''), 'ACH'), 'ach install.json name');

$controller = dirname(__DIR__) . '/src/api_cc/catalog/controller/payment/plugnpay_cc.php';
$src = is_file($controller) ? (string)file_get_contents($controller) : '';
assert_true(str_contains($src, 'class PlugnpayCc'), 'cc catalog class name');
assert_true(str_contains($src, 'RemoteApi::buildCreditFields'), 'cc uses protocol helper');
assert_true(str_contains($src, 'approvedOrderStatusId'), 'cc catalog uses order status resolver');
assert_true(str_contains($src, 'CURLOPT_SSL_VERIFYPEER') === false, 'cc controller does not disable TLS itself');

$ach_controller = dirname(__DIR__) . '/src/api_ach/catalog/controller/payment/plugnpay_ach.php';
$ach_src = is_file($ach_controller) ? (string)file_get_contents($ach_controller) : '';
assert_true(str_contains($ach_src, 'class PlugnpayAch'), 'ach catalog class name');
assert_true(str_contains($ach_src, 'RemoteApi::buildAchFields'), 'ach uses protocol helper');
assert_true(str_contains($ach_src, 'approvedOrderStatusId'), 'ach catalog uses order status resolver');

$cc_admin = (string)file_get_contents(dirname(__DIR__) . '/src/api_cc/admin/controller/payment/plugnpay_cc.php');
$ach_admin = (string)file_get_contents(dirname(__DIR__) . '/src/api_ach/admin/controller/payment/plugnpay_ach.php');
$ach_api = (string)file_get_contents(dirname(__DIR__) . '/src/api_ach/system/library/remote_api.php');
assert_true(str_contains($cc_admin, 'function install'), 'cc admin install defaults order status');
assert_true(str_contains($ach_admin, 'function install'), 'ach admin install defaults order status');
assert_true(str_contains($ach_api, 'function resolveOrderStatusId'), 'ach protocol resolves Pending');

if ($failed > 0) {
	echo "\n{$failed} check(s) failed\n";
	exit(1);
}

echo "\nAll checks passed\n";
exit(0);
