<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class PdoTransaction implements Transaction {

	private $database;

	public function __construct(\PDO $database) {
		$this->database = $database;
	}

	/**
	 * @return mixed
	 */
	public function begin(\Closure $closure) {
		$this->database->exec('START TRANSACTION');
		try {
			$result = $closure();
			$this->database->exec('COMMIT TRANSACTION');
			return $result;
		} catch (\Throwable $exception) {
			$this->database->exec('ROLLBACK TRANSACTION');
			throw $exception;
		}
	}
}