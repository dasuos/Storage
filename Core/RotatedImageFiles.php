<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class RotatedImageFiles implements Files {

	private const NO_FLAG = 0;
	private const EXIF_FLIP_FLAGS = [7, 5, 4,];
	private const EXIF_ORIENTATION_ANGLES = [
		8 => 90,
		7 => 90,
		6 => 270,
		5 => 270,
		4 => 180,
		3 => 180,
	];


	private $origin;
	private $path;
	private $image;

	public function __construct(Files $origin, Path $path, Image $image) {
		$this->origin = $origin;
		$this->path = $path;
		$this->image = $image;
	}

	public function upload(
		string $name, string $tmp, int $size, int $error
	): void {
		$properties = $this->image->properties($tmp);
		$this->origin->upload($name, $tmp, $size, $error);
		$this->modify($this->path->location($name), $properties['Exif']);
	}

	public function delete(string $name): void {
		$this->origin->delete($name);
	}

	private function modify(string $path, array $exif): void {
		$image = @imagecreatefromjpeg($path);
		if ($image && isset($exif['Orientation'])) {
			$image = $this->rotate($image, $exif['Orientation']);
			$this->flip($image, $exif['Orientation']);
			imagejpeg($image, $path);
		}
	}

	private function rotate($image, int $orientation) {
		$angle = self::EXIF_ORIENTATION_ANGLES[$orientation] ?? self::NO_FLAG;
		return $angle ? imagerotate($image, $angle, 0) : $image;
	}

	private function flip($image, int $orientation) {
		return in_array($orientation, self::EXIF_FLIP_FLAGS) ?
			imageflip($image, IMG_FLIP_HORIZONTAL) : $image;
	}
}