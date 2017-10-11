<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class InformativeFile implements File {

	private $path;

	public function __construct(string $path) {
		$this->path = $path;
	}

	public function path(): string {
		if (!file_exists($this->path))
			throw new \UnexpectedValueException(
				'Given file path does not exist'
			);
		return $this->path;
	}

	public function content(): string {
		return file_get_contents($this->path());
	}

	public function properties(): array {
		return [
			'size' => filesize($this->path()),
			'mime' => finfo_file(
				finfo_open(FILEINFO_MIME_TYPE),
				$this->path()
			),
		];
	}
}