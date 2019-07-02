<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class SafeQuery implements Query {

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
		return $this->perform($sql, $placeholders)->fetchColumn(0);
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
			throw $this->exception($exception);
		}
	}

	private function exception(\Throwable $exception): \Throwable {
		$code = (int) $exception->getCode();
		if ($code === 23503)
			return new \Dasuos\Storage\ForeignKeyConstraintException(
				'Update or delete violates foreign key constraint',
				$code,
				$exception
			);
		elseif ($code === 23505)
			return new \Dasuos\Storage\UniqueConstraintException(
				'Duplicate column value violates unique constraint',
				$code,
				$exception
			);
		else return $exception;
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
