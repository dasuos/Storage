<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class ImageFiles implements Files {

	private $origin;
	private $dimensions;

	public function __construct(
		Files $origin, Dimensions $dimensions
	) {
		$this->origin = $origin;
		$this->dimensions = $dimensions;
	}

	public function upload(
		string $name, string $tmp, int $size, int $error
	): void {
		if ($this->dimensions->exceeding($tmp))
			throw new ImageFileException('Image file exceeds dimensions');
		$this->origin->upload($name, $tmp, $size, $error);
	}

	public function delete(string $name): void {
		$this->origin->delete($name);
	}
}