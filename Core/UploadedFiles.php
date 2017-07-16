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
		if (!$this->uploaded($error) || !is_uploaded_file($tmp))
			throw new \UnexpectedValueException(
				'Given file cannot be uploaded'
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

	private function uploaded(int $error): bool {
		return $error === UPLOAD_ERR_OK;
	}
}