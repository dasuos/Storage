<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class UploadedFiles implements Files {

	private $path;

	public function __construct(Path $path) {
		$this->path = $path;
	}

	public function save(
		string $name, string $tmp, int $size, int $error
	): void {
		if (!is_uploaded_file($tmp))
			throw new \UnexpectedValueException(
				'File must be uploaded via HTTP POST upload mechanism'
			);
		move_uploaded_file($tmp, $this->path->location($name));
	}

	public function delete(string $name): void {
		if (!file_exists($this->path->location($name)))
			throw new \UnexpectedValueException(
				'Given file does not exist and cannot be deleted'
			);
		unlink($this->path->location($name));
	}
}