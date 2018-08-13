<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class FakeQuery implements Query {

	private $origin;
	private $memory;

	public function __construct(Query $origin, array $memory) {
		$this->origin = $origin;
		$this->memory = $memory;
	}

	public function row(string $sql, array $placeholders = []): array {
		return $this->selective($sql) ?
			$this->memory :
			$this->origin->row($sql, $placeholders);
	}

	public function rows(string $sql, array $placeholders = []): array {
		return $this->selective($sql) ?
			$this->memory :
			$this->origin->rows($sql, $placeholders);
	}

	/**
	 * @return mixed
	 */
	public function column(string $sql, array $placeholders = []) {
		return $this->selective($sql) ?
			$this->memory :
			$this->origin->column($sql, $placeholders);
	}

	/**
	 * @return mixed
	 */
	public function perform(string $sql, array $placeholders = []) {
		return $this->selective($sql) ?
			$this->memory :
			$this->origin->perform($sql, $placeholders);
	}

	private function selective(string $sql): bool {
		return (bool) preg_match('~^SELECT\s+[a-z_*]~i', $sql) &&
			!preg_match('~^SELECT\s+[\w\d_]+\(~i', $sql);
	}
}