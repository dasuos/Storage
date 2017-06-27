<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class ImageFiles implements Files {

	private const EXTENSIONS = ['image/gif', 'image/png', 'image/jpeg'];

	private const MAX_WIDTH = 2000;
	private const MAX_HEIGHT = 2000;

	private $origin;
	private $extensions;

	public function __construct(Files $origin, Extensions $extensions) {
		$this->origin = $origin;
		$this->extensions = $extensions;
	}

	public function upload(
		string $name, string $tmp, int $size, int $error
	): void {
		if (!$this->extensions->allowed($tmp, self::EXTENSIONS))
			throw new FileUploadException('Given file has prohibit extension');
		if ($this->exceeding($tmp))
			throw new ImageFileException('Image file exceeds dimensions');
		$this->origin->upload($name, $tmp, $size, $error);
	}

	public function delete(string $name): void {
		$this->origin->delete($name);
	}

	private function size(string $tmp): array {
		$size = @getimagesize($tmp);
		if (!@is_array($size))
			throw new ImageFileException(
				'Image file is unreadable or does not have supporting format'
			);
		return $size;
	}

	private function exceeding(string $tmp): bool {
		$size = $this->size($tmp);
		return ($size[0] > self::MAX_WIDTH) || ($size[1] > self::MAX_HEIGHT);
	}


}