<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class FilePath implements Path {

	private $directory;

	public function __construct(string $directory) {
		$this->directory = $directory;
	}

	public function location(string $file): string {
		if (!file_exists($this->directory))
			throw new \UnexpectedValueException('Directory path is invalid');
		return $this->directory . DIRECTORY_SEPARATOR . basename($file);
	}
}