<?php
namespace Dasuos\Storage;

final class SafeQuery implements Query {

	private const UNIQUE_CONSTRAINT = '23505';

	private $database;

	public function __construct(DefinedPdo $database) {
		$this->database = $database;
	}

	public function row(string $sql, array $placeholders = []): array {
		return $this->perform($sql, $placeholders)->fetch();
	}

	public function rows(string $sql, array $placeholders = []): array {
		return $this->perform($sql, $placeholders)->fetchAll();
	}

	public function column(string $sql, array $placeholders = []) {
		return $this->perform($sql, $placeholders)->fetchColumn();
	}

	public function perform(
		string $sql, array $placeholders = []
	): \PDOStatement {
		try {
			return $placeholders ? $this->statement($sql, $placeholders) :
				$this->database->query($sql);
		} catch (\PDOException $exception) {
			throw $this->exception($exception);
		}
	}

	private function statement(
		string $sql, array $placeholders
	): \PDOStatement {
		$statement = $this->database->prepare($sql);
		$statement->execute($placeholders);
		return $statement;
	}

	private function exception(\Throwable $exception): \Throwable {
		return $exception->getCode() === self::UNIQUE_CONSTRAINT ?
			new UniqueConstraintException(
				'Duplicate column value violates unique constraint',
				(int) self::UNIQUE_CONSTRAINT,
				$exception
			) :
			$exception;
	}
}