<?php
/**
 * PlugnPay Smart Screens v2 debug logger with secret redaction.
 *
 * @copyright Copyright (c) PlugnPay Technologies
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0
 */

declare(strict_types=1);

namespace Opencart\System\Library\Extension\PlugnpaySs2;

class SmartScreensLogger {
	/**
	 * @var list<string>
	 */
	private const SENSITIVE_KEYS = [
		'card-number',
		'card_number',
		'card-cvv',
		'card_cvv',
		'publisher-password',
		'publisher_password',
		'pt_card_number',
		'cc_number',
		'cc_cvv',
	];

	private string $log_dir;
	private bool $enabled;

	public function __construct(string $log_dir, bool $enabled = false) {
		$this->log_dir = rtrim($log_dir, '/\\');
		$this->enabled = $enabled;
	}

	public function isEnabled(): bool {
		return $this->enabled;
	}

	/**
	 * @param array<string, mixed> $context
	 */
	public function log(string $message, array $context = []): void {
		if (!$this->enabled) {
			return;
		}

		if ($this->log_dir !== '' && !is_dir($this->log_dir)) {
			@mkdir($this->log_dir, 0755, true);
		}

		$line = date('Y-m-d H:i:s') . ' ' . $message;

		if ($context !== []) {
			$line .= "\n" . print_r($this->sanitize($context), true);
		}

		$line .= "\n----------------------------------------\n";

		$file = $this->log_dir . '/plugnpay_ss2_' . date('Ymd') . '.log';
		@file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
	}

	/**
	 * @param array<string, mixed> $data
	 *
	 * @return array<string, mixed>
	 */
	public function sanitize(array $data): array {
		$out = [];

		foreach ($data as $key => $value) {
			$key_str = (string)$key;

			if ($this->isSensitiveKey($key_str)) {
				if (stripos($key_str, 'number') !== false && is_string($value) && strlen($value) >= 4) {
					$out[$key] = str_repeat('X', max(0, strlen($value) - 4)) . substr($value, -4);
				} else {
					$out[$key] = '***REDACTED***';
				}

				continue;
			}

			if (is_array($value)) {
				$out[$key] = $this->sanitize($value);
			} else {
				$out[$key] = $value;
			}
		}

		return $out;
	}

	private function isSensitiveKey(string $key): bool {
		$normalized = strtolower(str_replace('_', '-', $key));

		foreach (self::SENSITIVE_KEYS as $sensitive) {
			if ($normalized === strtolower(str_replace('_', '-', $sensitive))) {
				return true;
			}
		}

		return false;
	}
}
