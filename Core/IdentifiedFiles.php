<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class IdentifiedFiles implements Files {

	private $origin;
	private $types;

	public function __construct(Files $origin, array $types) {
		$this->origin = $origin;
		$this->types = $types;
	}

	public function save(
		string $name,
		string $tmp,
		int $size,
		int $error
	): void {
		if (!$this->identified($tmp))
			throw new \UnexpectedValueException(
				'Given file has prohibit format'
			);
		$this->origin->save($name, $tmp, $size, $error);
	}

	public function delete(string $name): void {
		$this->origin->delete($name);
	}

	public function identified(string $tmp): bool {
		return in_array(
			finfo_file(finfo_open(FILEINFO_MIME_TYPE), $tmp),
			$this->types,
			true
		);
	}
}