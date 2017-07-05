<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class FakeFiles implements Files {

	public function upload(
		string $name, string $tmp, int $size, int $error
	): void {
	}

	public function delete(string $name): void {
	}
}