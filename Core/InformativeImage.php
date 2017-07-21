<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class InformativeImage implements Image {

	private const WIDTH = 0;
	private const HEIGHT = 1;

	public function properties(string $path): array {
		$size = $this->size($path);
		$exif = $this->exif($path);
		return [
			'width' => $size[self::WIDTH],
			'height' => $size[self::HEIGHT],
			'exif'=> $exif
		];
	}

	private function size(string $path): array {
		$size = @getimagesize($path);
		if (!is_array($size))
			throw new \UnexpectedValueException(
				'Image is unreadable or does not have supporting format'
			);
		return $size;
	}

	private function exif(string $path): array {
		$exif = @exif_read_data($path);
		return $exif ? $exif : [];
	}
}