<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class PdoTransaction implements Transaction {

	private $pdo;

	public function __construct(\PDO $pdo) {
		$this->pdo = $pdo;
	}

	/**
	 * @return mixed
	 */
	public function begin(\Closure $closure) {
		try {
			$this->pdo->beginTransaction();
			$result = $closure();
			$this->pdo->commit();
			return $result;
		} catch (\Throwable $exception) {
			$this->pdo->rollBack();
			throw $exception;
		}
	}
}