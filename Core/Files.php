<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

interface Files {
	public function save(
		string $name, string $path, int $size, int $error
	): void;
	public function delete(string $name): void;
}