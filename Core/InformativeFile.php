<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class InformativeFile implements File {

	private $path;
	private $name;

	public function __construct(Path $path, string $name) {
		$this->path = $path;
		$this->name = $name;
	}

	public function path(): string {
		return $this->path->location($this->name);
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