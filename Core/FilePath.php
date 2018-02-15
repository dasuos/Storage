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
        $this->validate($location);
		return $location;
	}

	public function directory(): string {
        $this->validate($this->directory);
        return $this->directory;
    }

    private function validate(string $path): void {
        if (!file_exists($path))
            throw new \UnexpectedValueException(
                'Given file path does not exist'
            );
    }
}