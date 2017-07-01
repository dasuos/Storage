<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class StrictFiles implements Files {

	private const MAXIMUM_SIZE = 2000000;

	private $origin;
	private $path;
	private $extensions;

	public function __construct(Files $origin, Path $path, array $extensions) {
		$this->origin = $origin;
		$this->path = $path;
		$this->extensions = $extensions;
	}

	public function upload(
		string $name, string $tmp, int $size, int $error
	): void {
		$path = $this->path->location($name);
		if (!$this->valid($path, $tmp, $size, $error))
			throw new FileUploadException(
				'Given file already exists, exceeds maximum size, 
				has prohibit extension or cannot be uploaded'
			);
		$this->origin->upload($name, $tmp, $size, $error);
		$this->permit($path);
	}

	public function delete(string $name): void {
		if (!file_exists($this->path->location($name)))
			throw new FileDeletionException(
				'Given file does not exist and cannot be deleted'
			);
		$this->origin->delete($name);
	}

	private function valid(
		string $path, string $tmp, int $size, int $error
	): bool {
		return $this->uploaded($error)
			&& !file_exists($path)
			&& !$this->exceeding($size)
			&& $this->allowed($tmp);
	}

	private function uploaded(int $error): bool {
		return $error === UPLOAD_ERR_OK;
	}

	private function exceeding(int $size): bool {
		return $size > self::MAXIMUM_SIZE;
	}

	private function allowed(string $tmp): bool {
		return in_array(
			finfo_file(finfo_open(FILEINFO_MIME_TYPE), $tmp),
			$this->extensions,
			true
		);
	}

	private function permit(string $path): void {
		@chmod($path, 0666);
	}
}

