<?php
declare(strict_types = 1);

namespace Dasuos\Storage;

interface Query {

	public function row(string $sql, array $placeholders = []): array;
	public function rows(string $sql, array $placeholders = []): array;
	/**
	 * @return mixed
	 */
	public function column(string $sql, array $placeholders = []);
	/**
	 * @return mixed
	 */
	public function perform(
		string $sql,
		array $placeholders = []
	);
}
