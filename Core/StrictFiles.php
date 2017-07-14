<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class StrictFiles implements Files {

	private $origin;
	private $path;
	private $extensions;
	private $size;

	public function __construct(
		Files $origin, Path $path, Extensions $extensions, int $size
	) {
		$this->origin = $origin;
		$this->path = $path;
		$this->extensions = $extensions;
		$this->size = $size;
	}

	public function save(
		string $name, string $tmp, int $size, int $error
	): void {
		$path = $this->path->location($name);
		if (!$this->valid($path, $tmp, $size, $error))
			throw new \UnexpectedValueException(
				'Given file already exists, exceeds maximum size, 
				has prohibit extension or cannot be uploaded'
			);
		$this->origin->save($name, $tmp, $size, $error);
		$this->permit($path);
	}

	public function delete(string $name): void {
		$this->origin->delete($name);
	}

	private function valid(
		string $path, string $tmp, int $size, int $error
	): bool {
		return $this->uploaded($error)
			&& !file_exists($path)
			&& !$this->exceeding($size)
			&& $this->extensions->allowed($tmp);
	}

	private function uploaded(int $error): bool {
		return $error === UPLOAD_ERR_OK;
	}

	private function exceeding(int $size): bool {
		return $size > $this->size;
	}

	private function permit(string $path): void {
		@chmod($path, 0666);
	}
}

