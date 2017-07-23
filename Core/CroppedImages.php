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
		if (!$this->valid(
			$properties['mime'], $properties['width'], $properties['height']
		))
			throw new \UnexpectedValueException(
				'Image does not have valid format or size'
			);
		$this->origin->save($name, $path, $size, $error);
		$this->crop(
			$this->path->location($name),
			$properties['mime'],
			$properties['width'],
			$properties['height']
		);
	}

	public function delete(string $name): void {
		$this->origin->delete($name);
	}

	private function crop(
		string $path, string $mime, int $originWidth, int $originHeight
	): void {
		$thumbnail = $this->thumbnail($mime);
		$width = $this->width($originWidth, $originHeight);
		$height = $this->height($originWidth, $originHeight);
		imagecopyresampled(
			$thumbnail, $this->identifier($path, $mime),
			$this->centeredCoordinate($width, $this->width),
			$this->centeredCoordinate($height, $this->height),
			0, 0,
			$width, $height,
			$originWidth, $originHeight
		);
		$this->store($thumbnail, $path, $mime);
	}

	private function valid(string $mime, int $width, int $height): bool {
		return array_key_exists($mime, self::VALID_TYPES)
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

	private function thumbnail(string $mime) {
		$thumbnail = imagecreatetruecolor($this->width, $this->height);
		if ($mime === 'image/png')
			$this->transparentize($thumbnail);
		return $thumbnail;
	}

	private function transparentize($thumbnail): void {
		imagealphablending($thumbnail, false);
		imagefilledrectangle(
			$thumbnail,
			0, 0, $this->width, $this->height,
			imagecolorallocatealpha($thumbnail, 0, 0, 0, 127)
		);
		imagesavealpha($thumbnail, true);
	}

	/**
	 * @return mixed
	 */

	private function identifier(string $path, string $mime) {
		$function = 'imagecreatefrom'. self::VALID_TYPES[$mime];
		return $function($path);
	}

	private function store($thumbnail, string $path, string $mime): void {
		$function = 'image'. self::VALID_TYPES[$mime];
		$function($thumbnail, $path, $mime === 'image/png' ? 9 : 100);
	}
}

