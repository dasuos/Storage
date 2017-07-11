<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class RotatedImage implements Image {

	private const EXIF_FLAG_ANGLES = [8 => 90, 6 => -90, 3 => 180,];

	private $origin;

	public function __construct(Image $origin) {
		$this->origin = $origin;
	}

	public function properties(string $path): array {
		$properties = $this->origin->properties($path);
		$this->rotate($path, $properties['Exif']);
		return $properties;
	}

	private function rotate(string $path, array $exif): void {
		$image = imagecreatefromjpeg($path);
		if ($image && $exif && isset($exif['Orientation'])) {
			imagerotate(
				$image,
				self::EXIF_FLAG_ANGLES[$exif['Orientation']],
				0
			);
			imagejpeg($image, $path);
		}
	}
}