<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

interface Path {
	public function reference(string $name): string;
}