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
		$thumbnail = $this->thumbnail();

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
		$wider = $this->widerThanOrigin($width, $height);
		return $wider ? $this->height :
			$this->integer($height / ($width / $this->width));
	}

	private function width(int $width, int $height): int {
		$wider = $this->widerThanOrigin($width, $height);
		return $wider ? $this->integer($width / ($height / $this->height)) :
			$this->width;
	}

	private function widerThanOrigin(int $width, int $height): bool {
		return $this->aspect($width, $height) >=
			$this->aspect($this->width, $this->height);
	}

	private function aspect(int $width, int $height): float {
		return $width / $height;
	}

	private function thumbnail() {
		return imagecreatetruecolor($this->width, $this->height);
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

