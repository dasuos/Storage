<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class OversizedFiles implements Files {

	private const BYTE_PATTERN = '/^\d+(k|M|G|T)?B$/';
	private const BYTE_MULTIPLE_PATTERN = '/(k|M|G|T)?B/';
	private const BYTE_SIZES = [
		'B' => 1,
		'kB' => 1e3,
		'MB' => 1e6,
		'GB' => 1e9,
		'TB' => 1e12,
	];

	private $origin;
	private $limit;

	public function __construct(Files $origin, string $limit) {
		$this->origin = $origin;
		$this->limit = $limit;
	}

	public function save(
		string $name, string $tmp, int $size, int $error
	): void {
		if ($this->oversized($size, $this->limit))
			throw new \UnexpectedValueException(
				'Given file exceeds maximum allowed size'
			);
		$this->origin->save($name, $tmp, $size, $error);
	}

	public function delete(string $name): void {
		$this->origin->delete($name);
	}

	private function oversized(int $size, string $limit): bool {
		return $size > $this->size($limit);
	}

	private function size(string $limit): int {
		if ($this->inBytes($limit))
			return $this->conversion($limit);
		throw new \UnexpectedValueException(
			'Expected string value in byte format'
		);
	}

	private function inBytes(string $limit): bool {
		return is_string($limit) &&
			preg_match(self::BYTE_PATTERN, $limit);
	}

	public function conversion(string $limit): int {
		return ($this->number($limit) *
			(int) self::BYTE_SIZES[$this->multiple($limit)]);
	}

	private function number(string $limit): int {
		return (int) preg_replace(self::BYTE_MULTIPLE_PATTERN, '', $limit);
	}

	private function multiple(string $limit): string {
		preg_match(self::BYTE_MULTIPLE_PATTERN, $limit, $match);
		return $match[0];
	}
}