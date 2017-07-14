<?php
declare(strict_types = 1);
namespace Dasuos\Tests\TestCase;

final class CopiedFiles {

	private const ALL = '*';

	private $from;
	private $to;

	public function __construct(string $from, string $to) {
		$this->from = $from;
		$this->to = $to;
	}

	public function copy() {
		if (!file_exists($this->from) || !file_exists($this->to))
			throw new \UnexpectedValueException(
				'One or more directory paths are not valid'
			);
		foreach ($this->all($this->from) as $file)
			copy(
				$this->path($this->from, $file),
				$this->path($this->to, $file)
			);
	}

	private function path(string $directory, string $file) {
		return $directory . DIRECTORY_SEPARATOR . $file;
	}

	private function all(string $directory): array {
		return array_map(
			'basename', glob($directory . DIRECTORY_SEPARATOR . self::ALL)
		);
	}
}