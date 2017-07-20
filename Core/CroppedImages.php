<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class CroppedImages implements Files {

	private $origin;
	private $path;
	private $width;
	private $height;

	public function __construct(
		Files $origin, Path $path, int $width, int $height
	) {
		$this->origin = $origin;
		$this->path = $path;
		$this->width = $width;
		$this->height = $height;
	}

	public function save(
		string $name, string $path, int $size, int $error
	): void {
		$this->origin->save($name, $path, $size, $error);
		$this->crop($this->path->location($name));
	}

	public function delete(string $name): void {
		$this->origin->delete($name);
	}

	private function crop($path) {
		$image = imagecreatefromjpeg($path);
		$thumbnail = imagecreatetruecolor($this->width, $this->height);

		$originWidth = imagesx($image);
		$originHeight = imagesy($image);

		$width = $this->width(
			$originWidth, $originHeight
		);
		$height = $this->height(
			$originWidth, $originHeight
		);
		imagecopyresampled(
			$thumbnail, $image,
			$this->centeredHorizontal($height),
			$this->centeredVertical($width),
			0, 0,
			$width, $height,
			$originWidth, $originHeight
		);
		imagejpeg($thumbnail, $path);
	}

	private function height(int $width, int $height): int {
		return $this->widerThanOrigin($width, $height) ?
			$this->integer($height / ($width / $this->width)) : $this->height;
	}

	private function width(int $width, int $height): int {
		return $this->widerThanOrigin($width, $height) ?
			$this->width : $this->integer($width / ($height / $this->height));
	}

	private function widerThanOrigin(int $width, int $height): bool {
		return $width / $height < $this->width /$this->height;
	}

	private function centeredHorizontal(int $width): int {
		return $this->integer((0 - ($width - $this->width) / 2));
	}

	private function centeredVertical(int $height): int {
		return $this->integer((0 - ($height - $this->height) / 2));
	}

	private function integer($number) {
		return (int) round($number);
	}
}

