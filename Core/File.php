<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

interface File {
	public function path(): string;
	public function properties(): array;
}