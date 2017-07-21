<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class MeasuredImages implements Files {

	private $origin;
	private $image;
	private $width;
	private $height;

	public function __construct(
		Files $origin, int $width, int $height
	) {
		$this->origin = $origin;
		$this->width = $width;
		$this->height = $height;
	}

	public function save(
		string $name, string $tmp, int $size, int $error
	): void {
		$properties = (new InformativeImage)->properties($tmp);
		if ($this->exceeding($properties['width'], $properties['height']))
			throw new \UnexpectedValueException(
				'Image exceeds maximum dimensions'
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