<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class FileExtensions implements Extensions {

	private $list;

	public function __construct(array $list) {
		$this->list = $list;
	}

	public function allowed(string $tmp): bool {
		return in_array(
			finfo_file(
				finfo_open(FILEINFO_MIME_TYPE), $tmp
			), $this->list, true
		);
	}
}