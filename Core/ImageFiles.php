<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class ImageFiles implements Files {

	private const MAXIMUM_WIDTH = 2000;
	private const MAXIMUM_HEIGHT = 2000;

	private $origin;
	private $image;

	public function __construct(Files $origin, Image $image) {
		$this->origin = $origin;
		$this->image = $image;
	}

	public function upload(
		string $name, string $tmp, int $size, int $error
	): void {
		$properties = $this->image->properties($tmp);
		if ($this->exceeding($properties['Width'], $properties['Height']))
			throw new \UnexpectedValueException(
				'Image file exceeds maximum dimensions'
			);
		$this->origin->upload($name, $tmp, $size, $error);
	}

	public function delete(string $name): void {
		$this->origin->delete($name);
	}

	private function exceeding(int $width, int $height): bool {
		return ($width > self::MAXIMUM_WIDTH) ||
			($height > self::MAXIMUM_HEIGHT);
	}
}