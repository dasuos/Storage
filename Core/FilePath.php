<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class FilePath implements Path {

	private $directory;

	public function __construct(string $directory) {
		$this->directory = $directory;
	}
	public function reference($name) {
		return $this->directory . DIRECTORY_SEPARATOR . basename($name);
	}
}