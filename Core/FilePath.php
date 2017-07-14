<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class FilePath implements Path {

	private $directory;

	public function __construct(string $directory) {
		$this->directory = $directory;
	}

	public function location(string $file): string {
		$path = $this->directory . DIRECTORY_SEPARATOR . basename($file);
		if (!file_exists($path))
			throw new \UnexpectedValueException('File path is invalid');
		return $path;
	}
}