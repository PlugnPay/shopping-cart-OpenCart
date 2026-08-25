<?php
/**
 * Pure Smart Screens v2 protocol helpers (no OpenCart dependencies).
 *
 * @copyright Copyright (c) PlugnPay Technologies
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0
 */

declare(strict_types=1);

namespace Opencart\System\Library\Extension\PlugnpaySs2;

class SmartScreensProtocol {
	public const ENDPOINT = 'https://pay1.plugnpay.com/pay/';
	public const CLIENT_IDENTIFIER = 'OpenCart_SS2';
	public const CUSTOM_SESSION = 'ocsess';
	public const CUSTOM_ORDER_ID = 'ocorderid';
	public const VERSION = '1.0.1';

	/**
	 * @param array<string, mixed> $response
	 */
	public static function extractCustomValue(array $response, string $name): string {
		$name = strtolower(trim($name));

		for ($i = 1; $i <= 10; $i++) {
			$name_key = 'pt_custom_name_' . $i;
			$value_key = 'pt_custom_value_' . $i;

			if (!isset($response[$name_key])) {
				continue;
			}

			if (strtolower(trim((string)$response[$name_key])) === $name) {
				return isset($response[$value_key]) ? (string)$response[$value_key] : '';
			}
		}

		if (isset($response[$name])) {
			return (string)$response[$name];
		}

		return '';
	}

	/**
	 * @param array<string, mixed> $response
	 */
	public static function isSuccessfulResponse(array $response): bool {
		$status = strtolower(trim((string)($response['pi_response_status'] ?? '')));

		return $status === 'success';
	}

	/**
	 * @param mixed $returned_amount
	 * @param mixed $expected_amount
	 */
	public static function amountsMatch($returned_amount, $expected_amount): bool {
		$a = number_format((float)$returned_amount, 2, '.', '');
		$b = number_format((float)$expected_amount, 2, '.', '');

		return $a === $b;
	}

	/**
	 * Prefer a saved status, else the OpenCart "Pending" status, else the store default.
	 *
	 * @param list<array<string, mixed>> $order_statuses
	 */
	public static function resolveOrderStatusId(int $saved, array $order_statuses, int $store_default = 0): int {
		if ($saved > 0) {
			return $saved;
		}

		foreach ($order_statuses as $status) {
			if (!is_array($status)) {
				continue;
			}

			$name = (string)($status['name'] ?? '');
			$id = (int)($status['order_status_id'] ?? 0);

			if ($id > 0 && strcasecmp($name, 'Pending') === 0) {
				return $id;
			}
		}

		if ($store_default > 0) {
			return $store_default;
		}

		return 1;
	}

	/**
	 * Build hosted-fields map for POST to https://pay1.plugnpay.com/pay/
	 *
	 * @param array<string, mixed> $input
	 *
	 * @return array<string, string>
	 */
	public static function buildHostedFields(array $input): array {
		$order_id = (string)$input['order_id'];
		$currency = (string)$input['currency'];
		$session_id = (string)$input['session_id'];

		return [
			'pt_gateway_account'            => trim((string)$input['gateway_account']),
			'pt_transaction_amount'         => (string)$input['amount'],
			'pt_currency'                   => $currency,
			'pt_currency_code'              => $currency,
			'pb_post_auth'                  => 'no',
			'pt_account_code_1'             => $order_id,
			'pt_billing_company'            => (string)($input['billing_company'] ?? ''),
			'pt_payment_name'               => (string)($input['billing_name'] ?? ''),
			'pt_billing_address_1'          => (string)($input['billing_address_1'] ?? ''),
			'pt_billing_address_2'          => (string)($input['billing_address_2'] ?? ''),
			'pt_billing_city'               => (string)($input['billing_city'] ?? ''),
			'pt_billing_state'              => (string)($input['billing_state'] ?? ''),
			'pt_billing_postal_code'        => (string)($input['billing_postal_code'] ?? ''),
			'pt_billing_country'            => (string)($input['billing_country'] ?? ''),
			'pt_billing_phone_number'       => (string)($input['billing_phone'] ?? ''),
			'pt_billing_email_address'      => (string)($input['billing_email'] ?? ''),
			'pt_client_identifier'          => self::CLIENT_IDENTIFIER,
			'pt_ip_address'                 => (string)($input['ip_address'] ?? ''),
			'pb_transition_type'            => 'post',
			'pb_success_url'                => (string)$input['success_url'],
			'pd_collect_shipping_information' => 'no',
			'pd_display_items'              => 'no',
			'pt_custom_name_1'              => self::CUSTOM_SESSION,
			'pt_custom_value_1'             => $session_id,
			'pt_custom_name_2'              => self::CUSTOM_ORDER_ID,
			'pt_custom_value_2'             => $order_id,
		];
	}
}
