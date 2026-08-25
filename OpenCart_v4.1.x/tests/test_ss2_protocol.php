<?php
/**
 * CLI checks for SmartScreensProtocol and SmartScreensLogger (no OpenCart runtime).
 *
 * Run: php OpenCart_v4.1.x/tests/test_ss2_protocol.php
 */

declare(strict_types=1);

$base = dirname(__DIR__) . '/src/system/library';
require_once $base . '/smart_screens_protocol.php';
require_once $base . '/smart_screens_logger.php';

use Opencart\System\Library\Extension\PlugnpaySs2\SmartScreensLogger;
use Opencart\System\Library\Extension\PlugnpaySs2\SmartScreensProtocol;

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

/**
 * OpenCart 4 autoloader file mapping (system/engine/autoloader.php).
 */
function oc_class_to_file(string $class): string {
	return strtolower((string)preg_replace('~([a-z])([A-Z]|[0-9])~', '$1_$2', $class));
}

assert_true(oc_class_to_file('SmartScreens') === 'smart_screens', 'SmartScreens maps to smart_screens.php');
assert_true(oc_class_to_file('SmartScreensProtocol') === 'smart_screens_protocol', 'protocol class maps to file');
assert_true(oc_class_to_file('SmartScreensLogger') === 'smart_screens_logger', 'logger class maps to file');
assert_true(oc_class_to_file('PlugnpaySs2') === 'plugnpay_ss_2', 'digit class names do not match plugnpay_ss2.php');

$fields = SmartScreensProtocol::buildHostedFields([
	'gateway_account' => 'pnpdemo',
	'amount'          => '12.34',
	'currency'        => 'USD',
	'order_id'        => '42',
	'session_id'      => 'sess-id-1',
	'success_url'     => 'https://store.example/index.php?route=extension/plugnpay_ss2/payment/smart_screens.callback&order_id=42',
	'billing_name'    => 'Jane Doe',
	'billing_email'   => 'jane@example.com',
	'ip_address'      => '203.0.113.10',
]);

assert_true($fields['pt_gateway_account'] === 'pnpdemo', 'gateway account mapped');
assert_true($fields['pt_transaction_amount'] === '12.34', 'amount mapped');
assert_true($fields['pb_post_auth'] === 'no', 'authorization-only');
assert_true($fields['pb_transition_type'] === 'post', 'transition type post');
assert_true($fields['pd_display_items'] === 'no', 'no line items');
assert_true($fields['pt_client_identifier'] === 'OpenCart_SS2', 'client identifier');
assert_true($fields['pt_custom_name_1'] === SmartScreensProtocol::CUSTOM_SESSION, 'custom session name');
assert_true($fields['pt_custom_value_1'] === 'sess-id-1', 'custom session value');
assert_true($fields['pt_custom_name_2'] === SmartScreensProtocol::CUSTOM_ORDER_ID, 'custom order id name');
assert_true($fields['pt_custom_value_2'] === '42', 'custom order id value');
assert_true($fields['pt_account_code_1'] === '42', 'account code 1 is order id');
assert_true(str_contains($fields['pb_success_url'], 'smart_screens.callback'), 'success url includes callback route');
assert_true(SmartScreensProtocol::ENDPOINT === 'https://pay1.plugnpay.com/pay/', 'hosted endpoint');
assert_true(SmartScreensProtocol::VERSION === '1.0.1', 'protocol version');

$success = [
	'pi_response_status' => 'success',
	'pt_transaction_amount' => '12.34',
	'pt_custom_name_1' => 'ocsess',
	'pt_custom_value_1' => 'sess-id-1',
	'pt_custom_name_2' => 'ocorderid',
	'pt_custom_value_2' => '42',
];
assert_true(SmartScreensProtocol::isSuccessfulResponse($success), 'success status');
assert_true(!SmartScreensProtocol::isSuccessfulResponse(['pi_response_status' => 'badcard']), 'decline is not success');
assert_true(SmartScreensProtocol::amountsMatch('12.34', '12.340'), 'amount match ignores extra zeros');
assert_true(!SmartScreensProtocol::amountsMatch('12.34', '12.35'), 'amount mismatch');
assert_true(SmartScreensProtocol::extractCustomValue($success, 'ocsess') === 'sess-id-1', 'extract session custom');
assert_true(SmartScreensProtocol::extractCustomValue($success, 'ocorderid') === '42', 'extract order custom');
assert_true(SmartScreensProtocol::extractCustomValue(['ocsess' => 'flat'], 'ocsess') === 'flat', 'flat custom fallback');

$logger = new SmartScreensLogger(sys_get_temp_dir(), false);
$sanitized = $logger->sanitize([
	'pt_card_number' => '4111111111111111',
	'card_cvv' => '123',
	'publisher-password' => 'secret',
	'pt_gateway_account' => 'pnpdemo',
]);
assert_true($sanitized['pt_card_number'] === 'XXXXXXXXXXXX1111', 'PAN last four only');
assert_true($sanitized['card_cvv'] === '***REDACTED***', 'CVV redacted');
assert_true($sanitized['publisher-password'] === '***REDACTED***', 'password redacted');
assert_true($sanitized['pt_gateway_account'] === 'pnpdemo', 'account not redacted');

$install = dirname(__DIR__) . '/src/install.json';
$meta = is_file($install) ? json_decode((string)file_get_contents($install), true) : null;
assert_true(is_array($meta) && ($meta['name'] ?? '') === 'PlugnPay Smart Screens v2', 'install.json name');
assert_true(is_array($meta) && ($meta['version'] ?? '') === '1.0.1', 'install.json version');

$controller = dirname(__DIR__) . '/src/catalog/controller/payment/smart_screens.php';
$src = is_file($controller) ? (string)file_get_contents($controller) : '';
assert_true(str_contains($src, 'namespace Opencart\\Catalog\\Controller\\Extension\\PlugnpaySs2\\Payment;'), 'catalog namespace');
assert_true(str_contains($src, 'class SmartScreens'), 'catalog class name');
assert_true(str_contains($src, 'SmartScreensProtocol::buildHostedFields'), 'catalog uses protocol helper');
assert_true(!str_contains($src, 'class PlugnpaySs2'), 'catalog class has no digit suffix');

$admin = dirname(__DIR__) . '/src/admin/controller/payment/smart_screens.php';
$admin_src = is_file($admin) ? (string)file_get_contents($admin) : '';
assert_true(str_contains($admin_src, 'class SmartScreens'), 'admin class name');
assert_true(is_file(dirname(__DIR__) . '/src/admin/view/template/payment/smart_screens.twig'), 'admin twig exists');
assert_true(is_file(dirname(__DIR__) . '/src/catalog/view/template/payment/smart_screens.twig'), 'catalog twig exists');

if ($failed > 0) {
	echo "\n{$failed} check(s) failed\n";
	exit(1);
}

echo "\nAll checks passed\n";
exit(0);
