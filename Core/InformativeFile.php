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

	public function properties(): array {
		return [
			'Content' => file_get_contents($this->path()),
			'Size' => filesize($this->path()),
			'Extension' => $this->extension($this->path())
		];
	}

	private function extension(string $path) {
		return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
	}
}