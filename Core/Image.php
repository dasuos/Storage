<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

interface Image {
	public function properties(string $path): array;
}