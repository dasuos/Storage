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
		$directory = $this->path->directory();
		if (!$this->uploaded($tmp, $directory, $error)) {
			throw new \UnexpectedValueException(
				'Given file cannot be uploaded'
			);
		}
		$this->move(
		    $tmp,
            $directory . DIRECTORY_SEPARATOR . basename($name)
        );
	}

	public function delete(string $name): void {
		unlink($this->path->location($name));
	}

	private function uploaded(string $tmp, string $path, int $error): bool {
		return !file_exists($path)
			&& is_uploaded_file($tmp)
			&& $error === UPLOAD_ERR_OK;
	}

	private function move(string $tmp, string $location): void {
		move_uploaded_file($tmp, $location);
		@chmod($location, 0666);
	}
}
