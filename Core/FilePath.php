<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class FilePath implements Path {

	private $directory;

	public function __construct(string $directory) {
		$this->directory = $directory;
	}

	public function location(string $file): string {
		$location = $this->directory . DIRECTORY_SEPARATOR . basename($file);
		if (!file_exists($location)) {
			throw new \UnexpectedValueException(
				'Given file path does not exist'
			);
		}
		return $location;
	}
}