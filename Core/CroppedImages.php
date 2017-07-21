<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class CroppedImages implements Files {

	private const VALID_TYPES = [
		'image/jpeg' => 'jpeg',
		'image/png' => 'png',
		'image/gif' => 'gif',
	];

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
		$properties = (new InformativeImage)->properties($path);
		if (!$this->valid($path, $properties['width'], $properties['height']))
			throw new \UnexpectedValueException(
				'Image does not have valid format or size'
			);
		$this->origin->save($name, $path, $size, $error);
		$this->crop(
			$this->path->location($name),
			$properties['width'],
			$properties['height']
		);
	}

	public function delete(string $name): void {
		$this->origin->delete($name);
	}

	private function crop($path, $originWidth, $originHeight): void {
		$thumbnail = imagecreatetruecolor($this->width, $this->height);
		$width = $this->width($originWidth, $originHeight);
		$height = $this->height($originWidth, $originHeight);
		imagecopyresampled(
			$thumbnail, $this->identifier($path),
			$this->centeredCoordinate($width, $this->width),
			$this->centeredCoordinate($height, $this->height),
			0, 0,
			$width, $height,
			$originWidth, $originHeight
		);
		$this->store($thumbnail, $path);
	}

	private function valid(string $path, int $width, int $height): bool {
		return array_key_exists($this->mime($path), self::VALID_TYPES)
			&& $this->width <= $width && $this->height <= $height;
	}

	private function height(int $width, int $height): int {
		return $this->widerThanOrigin($width, $height) ?
			(int) round($height / ($width / $this->width)) : $this->height;
	}

	private function width(int $width, int $height): int {
		return $this->widerThanOrigin($width, $height) ? $this->width :
			(int) round($width / ($height / $this->height));
	}

	private function widerThanOrigin(int $width, int $height): bool {
		return $width / $height < $this->width / $this->height;
	}

	private function centeredCoordinate(int $minuend, int $subtrahend): int {
		return (int) round((0 - ($minuend - $subtrahend) / 2));
	}
	/**
	 * @return mixed
	 */
	private function identifier(string $path) {
		$function = 'imagecreatefrom'. self::VALID_TYPES[$this->mime($path)];
		return $function($path);
	}

	private function store($thumbnail, string $path): void {
		$function = 'image'. self::VALID_TYPES[$this->mime($path)];
		$function($thumbnail, $path);
	}

	private function mime(string $path): string {
		return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
	}
}

