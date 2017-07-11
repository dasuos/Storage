<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class InformativeImage implements Image {

	private const WIDTH = 0;
	private const HEIGHT = 1;

	public function properties(string $path): array {
		$size = $this->size($path);
		return [
			'Width' => $size[self::WIDTH],
			'Height' => $size[self::HEIGHT],
			'Exif'=> $this->exif($path)
		];
	}

	private function size(string $path): array {
		if (!file_exists($path) || !is_array($size = @getimagesize($path)))
			throw new \UnexpectedValueException(
				'Image file is unreadable or does not have supporting format'
			);
		return $size;
	}

	private function exif(string $path): array {
		$exif = $exif = @exif_read_data($path);
		return $exif ? $exif : [];
	}
}