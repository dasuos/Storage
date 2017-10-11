<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class UploadedFiles implements Files {

	private $path;

	public function __construct(Path $path) {
		$this->path = $path;
	}

	public function save(
		string $name,
		string $tmp,
		int $size,
		int $error
	): void {
		$path = $this->path->location($name);
		if (!$this->uploaded($tmp, $path, $error)) {
			throw new \UnexpectedValueException(
				'Given file cannot be uploaded'
			);
		}
		move_uploaded_file($tmp, $path);
		$this->permit($path);
	}

	public function delete(string $name): void {
		if (!file_exists($this->path->location($name))) {
			throw new \UnexpectedValueException(
				'Given file does not exist and cannot be deleted'
			);
		}
		unlink($this->path->location($name));
	}

	private function uploaded(string $tmp, string $path, int $error): bool {
		return !file_exists($path) && is_uploaded_file($tmp) &&
			$error === UPLOAD_ERR_OK;
	}

	private function permit(string $path): void {
		@chmod($path, 0666);
	}
}
