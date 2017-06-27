<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class FileExtensions implements Extensions {

	public function allowed(string $tmp, array $list): bool {
		return in_array(
			finfo_file(
				finfo_open(FILEINFO_MIME_TYPE), $tmp
			), $list, true
		);
	}
}