<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class StoredFiles implements Files {

	private $path;

	public function __construct(Path $path) {
		$this->path = $path;
	}

	public function upload(
		string $name, string $tmp, int $size, int $error
	): void {
		move_uploaded_file($tmp, $this->path->location($name));
	}

	public function delete(string $name): void {
		unlink($this->path->location($name));
	}
}