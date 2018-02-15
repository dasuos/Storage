<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class FakePath implements Path {

    private $directory;

    public function __construct(string $directory = '') {
        $this->directory = $directory;
    }

	public function location(string $path): string {
		return $path;
	}
    public function directory(): string {
        return $this->directory;
    }
}