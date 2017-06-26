<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

interface Files {
	public function upload(
		string $name, string $tmp, int $size, int $error
	): void;
	public function delete(string $name): void;
}