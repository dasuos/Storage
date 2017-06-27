<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class StrictFiles implements Files {

	private const MAXIMUM_SIZE = 2000;

	private $origin;
	private $path;

	public function __construct(Files $origin, Path $path) {
		$this->origin = $origin;
		$this->path = $path;
	}

	public function upload(
		string $name, string $tmp, int $size, int $error
	): void {
		$path = $this->path->reference($name);
		if (!$this->valid($path, $size, $error))
			throw new FileUploadException(
				'Given file already exists, exceeds maximum size 
				or cannot be uploaded'
			);
		$this->origin->upload($name, $tmp, $size, $error);
		$this->permit($path);
	}

	public function delete(string $name): void {
		if (!$this->existing($this->path->reference($name)))
			throw new FileDeletionException(
				'Given file does not exist and cannot be deleted'
			);
		$this->origin->delete($name);
	}

	private function valid(string $path, int $size, int $error): bool {
		return $this->uploaded($error)
			&& !$this->existing($path)
			&& !$this->exceeding($size);
	}

	private function uploaded(int $error): bool {
		return $error === UPLOAD_ERR_OK;
	}

	private function existing(string $path): bool {
		return file_exists($path);
	}

	private function exceeding(int $size): bool {
		return $size > self::MAXIMUM_SIZE;
	}

	private function permit(string $path): void {
		@chmod($path, 0666);
	}
}

