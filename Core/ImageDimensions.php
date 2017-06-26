<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class ImageDimensions implements Dimensions {

	private const WIDTH = 0;
	private const HEIGHT = 1;

	private $width;
	private $height;

	public function __construct($width, $height) {
		$this->width = $width;
		$this->height = $height;
	}

	public function exceeding(string $tmp): bool {
		$size = $this->reference($tmp);
		return ($size[self::WIDTH] > $this->width) ||
			($size[self::HEIGHT] > $this->height);
	}

	private function reference($tmp) {
		$size = @getimagesize($tmp);
		if (!@is_array($size))
			throw new ImageFileException(
				'Image file is unreadable or does not have supporting format'
			);
		return $size;
	}
}