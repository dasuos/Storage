<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

interface Dimensions {
	public function exceeding(string $entity): bool;
}