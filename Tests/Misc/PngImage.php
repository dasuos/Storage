<?php
declare(strict_types = 1);
namespace Dasuos\Storage\Misc;

final class PngImage {

	private const NAME = 'image.png';
	private const TEXT = 'Image for testing purpose';

	private $directory;
	private $width;
	private $height;

	public function __construct(string $directory, int $width, int $height) {
		$this->directory = $directory;
		$this->width = $width;
		$this->height = $height;
	}

	public function path(): string {
		$this->create(imagecreate($this->width, $this->height));
		return $this->location();
	}

	public function delete(): void {
		unlink($this->location());
	}

	private function create($image): void {
		$this->withBackground($image);
		$this->withText($image, $this->withColor($image));
		imagepng($image, $this->location());
		imagedestroy($image);
	}

	/**
	 * @return mixed
	 */
	private function withBackground($image) {
		return imagecolorallocate($image, 0x00, 0x00, 0x99);
	}

	/**
	 * @return mixed
	 */
	private function withColor($image) {
		return imagecolorallocate($image, 0xff, 0xff, 0xff);
	}

	/**
	 * @return mixed
	 */
	private function withText($image, $color) {
		return imagestring(
			$image,
			5,
			300,
			300,
			self::TEXT,
			$color
		);
	}

	private function location(): string {
		return $this->directory . DIRECTORY_SEPARATOR . self::NAME;
	}
}