<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class FakeFiles implements Files {

	public function save(
		string $name, string $path, int $size, int $error
	): void {
		//for testing purpose
	}

	public function delete(string $name): void {
		//for testing purpose
	}
}