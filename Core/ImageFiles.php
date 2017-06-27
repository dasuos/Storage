<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class ImageFiles implements Files {

	private const WIDTH = 0;
	private const HEIGHT = 1;

	private $origin;
	private $width;
	private $height;

	public function __construct(Files $origin, $width, $height) {
		$this->origin = $origin;
		$this->width = $width;
		$this->height = $height;
	}

	public function upload(
		string $name, string $tmp, int $size, int $error
	): void {
		if ($this->exceeding($tmp, $this->width, $this->height))
			throw new ImageUploadException(
				'Image file exceeds dimensions'
			);
		$this->origin->upload($name, $tmp, $size, $error);
	}

	public function delete(string $name): void {
		$this->origin->delete($name);
	}

	private function size(string $tmp): array {
		$size = getimagesize($tmp);
		if (!is_array($size))
			throw new ImageUploadException(
				'Image file is unreadable or does not have supporting format'
			);
		return $size;
	}

	private function exceeding(string $tmp, $width, $height): bool {
		$size = $this->size($tmp);
		return ($size[self::WIDTH] > $width) || ($size[self::HEIGHT] > $height);
	}


}