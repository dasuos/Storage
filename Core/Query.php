<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

interface Query {
	public function perform(
		string $sql,
		array $placeholders = []
	): \PDOStatement;
	/**
	 * @return mixed
	 */
	public function row(string $sql, array $placeholders = []);
	public function rows(string $sql, array $placeholders = []): array;
	/**
	 * @return mixed
	 */
	public function column(string $sql, array $placeholders = []);
}
