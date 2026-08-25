<?php
/**
 * PlugnPay Remote API client (pnpremote.cgi).
 *
 * Filename and class must not contain digits. OpenCart 4 maps class names to
 * files with preg_replace('~([a-z])([A-Z]|[0-9])~', '\1_\2', ...).
 *
 * @copyright Copyright (c) PlugnPay Technologies
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0
 * @see https://docs.plugnpay.com/docs/integration-specifications-documents/remote-api-integration-specification/
 */

declare(strict_types=1);

namespace Opencart\System\Library\Extension\PlugnpayApiCc;

class RemoteApi {
	public const ENDPOINT = 'https://pay1.plugnpay.com/payment/pnpremote.cgi';
	public const CLIENT_IDENTIFIER = 'OpenCart_API_CC';
	public const VERSION = '1.0.0';

	private string $publisher_name;
	private string $publisher_password;
	private ?RemoteApiLogger $logger;
	private string $last_raw_response = '';
	private int $comm_errno = 0;
	private string $comm_error = '';

	public function __construct(string $publisher_name, string $publisher_password, ?RemoteApiLogger $logger = null) {
		$this->publisher_name = $publisher_name;
		$this->publisher_password = $publisher_password;
		$this->logger = $logger;
	}

	public function getLastRawResponse(): string {
		return $this->last_raw_response;
	}

	public function getCommError(): string {
		return $this->comm_error;
	}

	/**
	 * @param array<string, mixed> $response
	 */
	public function isApproved(array $response): bool {
		$final = strtolower((string)($response['FinalStatus'] ?? ''));
		$success = strtolower((string)($response['success'] ?? ''));

		return $final === 'success' || $success === 'yes';
	}

	public static function normalizeAuthtype(string $authtype): string {
		$authtype = strtolower(trim($authtype));

		if (in_array($authtype, ['authpostauth', 'capture', 'sale', 'auth_capture'], true)) {
			return 'authpostauth';
		}

		return 'authonly';
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
	 * Hyphenated credit-card auth fields (LiteCart / CubeCart mapping).
	 *
	 * @param array<string, mixed> $input
	 *
	 * @return array<string, string>
	 */
	public static function buildCreditFields(array $input): array {
		$amount = (string)$input['amount'];
		$authtype = self::normalizeAuthtype((string)($input['authtype'] ?? 'authonly'));
		$order_id = (string)($input['order_id'] ?? '');

		$fields = [
			'mode'           => 'auth',
			'paymethod'      => 'credit',
			'authtype'       => $authtype,
			'easycart'       => '1',
			'shipinfo'       => !empty($input['ship_name']) ? '1' : '0',
			'orderID'        => $order_id,
			'acct_code'      => $order_id,
			'card-amount'    => $amount,
			'card-number'    => preg_replace('/\D/', '', (string)($input['card_number'] ?? '')) ?? '',
			'card-exp'       => (string)($input['card_exp'] ?? ''),
			'card-name'      => (string)($input['card_name'] ?? ''),
			'card-company'   => (string)($input['card_company'] ?? ''),
			'card-address1'  => (string)($input['card_address1'] ?? ''),
			'card-address2'  => (string)($input['card_address2'] ?? ''),
			'card-city'      => (string)($input['card_city'] ?? ''),
			'card-state'     => (string)($input['card_state'] ?? ''),
			'card-zip'       => (string)($input['card_zip'] ?? ''),
			'card-country'   => (string)($input['card_country'] ?? ''),
			'phone'          => (string)($input['phone'] ?? ''),
			'email'          => (string)($input['email'] ?? ''),
			'shipname'       => (string)($input['ship_name'] ?? ''),
			'address1'       => (string)($input['ship_address1'] ?? ''),
			'address2'       => (string)($input['ship_address2'] ?? ''),
			'city'           => (string)($input['ship_city'] ?? ''),
			'state'          => (string)($input['ship_state'] ?? ''),
			'zip'            => (string)($input['ship_zip'] ?? ''),
			'country'        => (string)($input['ship_country'] ?? ''),
			'shipping'       => (string)($input['shipping'] ?? '0.00'),
			'tax'            => (string)($input['tax'] ?? '0.00'),
			'currency'       => (string)($input['currency'] ?? ''),
			'ipaddress'      => (string)($input['ip_address'] ?? ''),
			'dontsndmail'    => 'yes',
			'client'         => (string)($input['client'] ?? self::CLIENT_IDENTIFIER),
		];

		$cvv = trim((string)($input['card_cvv'] ?? ''));

		if ($cvv !== '') {
			$fields['card-cvv'] = $cvv;
		}

		if ((float)$amount <= 0) {
			$fields['mode'] = 'checkcard';
			unset($fields['card-amount']);
		}

		self::appendLineItems($fields, $input['items'] ?? []);

		return $fields;
	}

	/**
	 * ACH / eCheck auth fields.
	 *
	 * @param array<string, mixed> $input
	 *
	 * @return array<string, string>
	 */
	public static function buildAchFields(array $input): array {
		$amount = (string)$input['amount'];
		$authtype = self::normalizeAuthtype((string)($input['authtype'] ?? 'authonly'));
		$order_id = (string)($input['order_id'] ?? '');
		$accttype = strtolower(trim((string)($input['accttype'] ?? 'checking')));

		if ($accttype !== 'savings') {
			$accttype = 'checking';
		}

		$fields = [
			'mode'           => 'auth',
			'paymethod'      => 'onlinecheck',
			'authtype'       => $authtype,
			'easycart'       => '1',
			'shipinfo'       => !empty($input['ship_name']) ? '1' : '0',
			'orderID'        => $order_id,
			'acct_code'      => $order_id,
			'card-amount'    => $amount,
			'card-name'      => (string)($input['account_name'] ?? ''),
			'card-company'   => (string)($input['card_company'] ?? ''),
			'card-address1'  => (string)($input['card_address1'] ?? ''),
			'card-address2'  => (string)($input['card_address2'] ?? ''),
			'card-city'      => (string)($input['card_city'] ?? ''),
			'card-state'     => (string)($input['card_state'] ?? ''),
			'card-zip'       => (string)($input['card_zip'] ?? ''),
			'card-country'   => (string)($input['card_country'] ?? ''),
			'phone'          => (string)($input['phone'] ?? ''),
			'email'          => (string)($input['email'] ?? ''),
			'shipname'       => (string)($input['ship_name'] ?? ''),
			'address1'       => (string)($input['ship_address1'] ?? ''),
			'address2'       => (string)($input['ship_address2'] ?? ''),
			'city'           => (string)($input['ship_city'] ?? ''),
			'state'          => (string)($input['ship_state'] ?? ''),
			'zip'            => (string)($input['ship_zip'] ?? ''),
			'country'        => (string)($input['ship_country'] ?? ''),
			'shipping'       => (string)($input['shipping'] ?? '0.00'),
			'tax'            => (string)($input['tax'] ?? '0.00'),
			'currency'       => (string)($input['currency'] ?? ''),
			'ipaddress'      => (string)($input['ip_address'] ?? ''),
			'dontsndmail'    => 'yes',
			'client'         => (string)($input['client'] ?? 'OpenCart_API_ACH'),
			'accttype'       => $accttype,
			'routingnum'     => preg_replace('/\D/', '', (string)($input['routingnum'] ?? '')) ?? '',
			'accountnum'     => preg_replace('/\D/', '', (string)($input['accountnum'] ?? '')) ?? '',
			'checknum'       => preg_replace('/\D/', '', (string)($input['checknum'] ?? '')) ?? '',
			'checktype'      => 'WEB',
		];

		if (!empty($input['business'])) {
			$fields['acctclass'] = 'business';
			$fields['commcardtype'] = 'business';
		}

		self::appendLineItems($fields, $input['items'] ?? []);

		return $fields;
	}

	/**
	 * @return array<string, string>
	 */
	public static function parseResponse(string $raw): array {
		$parsed = [];
		parse_str($raw, $parsed);

		$out = [];

		foreach ($parsed as $key => $value) {
			if (is_array($value)) {
				$out[(string)$key] = implode(',', $value);
			} else {
				$out[(string)$key] = (string)$value;
			}
		}

		return $out;
	}

	/**
	 * @param array<string, string|int|float> $fields
	 *
	 * @return array<string, string>
	 */
	public function request(array $fields): array {
		if (!function_exists('curl_init')) {
			$this->comm_errno = -1;
			$this->comm_error = 'PHP cURL extension is not available';
			$this->last_raw_response = '';

			if ($this->logger) {
				$this->logger->log('cURL missing', ['error' => $this->comm_error]);
			}

			return [
				'FinalStatus' => 'problem',
				'success'     => 'no',
				'MErrMsg'     => $this->comm_error,
			];
		}

		$payload = array_merge([
			'publisher-name'     => $this->publisher_name,
			'publisher-password' => $this->publisher_password,
		], $fields);

		foreach ($payload as $key => $value) {
			if ($value === null || $value === '') {
				unset($payload[$key]);
			}
		}

		$body = http_build_query($payload);

		if ($this->logger) {
			$this->logger->log('Request to PlugnPay', [
				'endpoint' => self::ENDPOINT,
				'fields'   => $payload,
			]);
		}

		$ch = curl_init(self::ENDPOINT);

		curl_setopt_array($ch, [
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => $body,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_CONNECTTIMEOUT => 15,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_SSL_VERIFYHOST => 2,
			CURLOPT_HTTPHEADER     => [
				'Content-Type: application/x-www-form-urlencoded',
			],
		]);

		$raw = curl_exec($ch);
		$this->comm_errno = (int)curl_errno($ch);
		$this->comm_error = (string)curl_error($ch);
		curl_close($ch);

		$this->last_raw_response = is_string($raw) ? $raw : '';

		if ($this->last_raw_response === '' || $this->comm_errno !== 0) {
			$response = [
				'FinalStatus' => 'problem',
				'success'     => 'no',
				'MErrMsg'     => $this->comm_error !== ''
					? $this->comm_error
					: 'Empty response from PlugnPay (check cURL connectivity / firewall)',
			];

			if ($this->logger) {
				$this->logger->log('Communication failure', [
					'errno' => $this->comm_errno,
					'error' => $this->comm_error,
				]);
			}

			return $response;
		}

		$response = self::parseResponse($this->last_raw_response);

		if ($this->logger) {
			$this->logger->log('Response from PlugnPay', [
				'FinalStatus' => $response['FinalStatus'] ?? '',
				'success'     => $response['success'] ?? '',
				'orderID'     => $response['orderID'] ?? ($response['orderid'] ?? ''),
				'auth-code'   => $response['auth-code'] ?? ($response['auth_code'] ?? ''),
				'resp-code'   => $response['resp-code'] ?? ($response['resp_code'] ?? ''),
				'MErrMsg'     => $response['MErrMsg'] ?? '',
				'avs-code'    => $response['avs-code'] ?? ($response['avs_code'] ?? ''),
				'cvvresp'     => $response['cvvresp'] ?? '',
			]);
		}

		return $response;
	}

	/**
	 * @param array<string, string>            $fields
	 * @param mixed                            $items
	 */
	private static function appendLineItems(array &$fields, $items): void {
		if (!is_array($items)) {
			return;
		}

		$j = 1;

		foreach ($items as $product) {
			if (!is_array($product)) {
				continue;
			}

			$fields['item' . $j] = (string)($product['sku'] ?? $product['model'] ?? '');
			$fields['cost' . $j] = number_format((float)($product['price'] ?? $product['cost'] ?? 0), 2, '.', '');
			$fields['quantity' . $j] = (string)($product['quantity'] ?? $product['qty'] ?? 1);
			$fields['description' . $j] = substr(strip_tags((string)($product['name'] ?? '')), 0, 255);
			$j++;
		}
	}
}
