<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

interface Extensions {
	public function allowed(string $path): bool;
}