<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class SafeQuery implements Query {

	private const CONSTRAINT_ERRORS = [
		'23503' => 'Update or delete violates foreign key constraint',
		'23505' => 'Duplicate column value violates unique constraint',
	];

	private $database;

	public function __construct(\PDO $database) {
		$this->database = $database;
	}

	public function row(string $sql, array $placeholders = []): array {
		$row = $this->perform($sql, $placeholders)->fetch();
		return $row ? $row : [];
	}

	public function rows(string $sql, array $placeholders = []): array {
		$rows = $this->perform($sql, $placeholders)->fetchAll();
		return $rows ? $rows : [];
	}

	/**
	 * @return mixed
	 */
	public function column(string $sql, array $placeholders = []) {
		return $this->perform($sql, $placeholders)->fetchColumn();
	}

	/**
	 * @return mixed
	 */
	public function perform(string $sql, array $placeholders = []) {
		try {
			return $placeholders
				? $this->statement($sql, $placeholders)
				: $this->database->query($sql);
		} catch (\PDOException $exception) {
			$code = (int) $exception->getCode();
			throw in_array(
				$code,
				array_keys(self::CONSTRAINT_ERRORS),
				true
			)
				? new \Dasuos\Storage\ConstraintException(
					self::CONSTRAINT_ERRORS[$code],
					$code,
					$exception
				)
				: $exception;
		}
	}

	private function statement(
		string $sql,
		array $placeholders
	): \PDOStatement {
		$statement = $this->database->prepare($sql);
		$statement->execute($placeholders);
		return $statement;
	}
}
