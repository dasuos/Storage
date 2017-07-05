<?php
declare(strict_types = 1);
namespace Dasuos\Tests\TestCase;

final class PngImage {

	private const PATH = __DIR__ . '/../temp/image.png';
	private const TEXT = 'Image for testing purpose';

	private $width;
	private $height;

	public function __construct(int $width, int $height) {
		$this->width = $width;
		$this->height = $height;
	}

	public function path(): string {
		$this->create(imagecreate($this->width, $this->height));
		return self::PATH;
	}

	private function create($image) {
		$this->withBackground($image);
		$this->withText($image, $this->withColor($image));
		imagepng($image, self::PATH);
		imagedestroy($image);
	}

	private function withBackground($image) {
		return imagecolorallocate($image, 0xFF, 0xCC, 0xDD);
	}

	private function withColor($image) {
		return imagecolorallocate($image, 133, 14, 91);
	}

	private function withText($image, $color) {
		return imagestring(
			$image, 5, 300, 300, self::TEXT, $color
		);
	}
}