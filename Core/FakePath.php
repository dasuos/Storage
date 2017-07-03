<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class FakePath implements Path {

	public function location(string $mock): string {
		return $mock;
	}
}