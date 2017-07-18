<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class MeasuredImages implements Files {

	private $origin;
	private $image;
	private $width;
	private $height;

	public function __construct(
		Files $origin, Image $image, int $width, int $height
	) {
		$this->origin = $origin;
		$this->image = $image;
		$this->width = $width;
		$this->height = $height;
	}

	public function save(
		string $name, string $tmp, int $size, int $error
	): void {
		$properties = $this->image->properties($tmp);
		if ($this->exceeding($properties['width'], $properties['height']))
			throw new \UnexpectedValueException(
				'Image file exceeds maximum dimensions'
			);
		$this->origin->save($name, $tmp, $size, $error);
	}

	public function delete(string $name): void {
		$this->origin->delete($name);
	}

	private function exceeding(int $width, int $height): bool {
		return $width > $this->width || $height > $this->height;
	}
}