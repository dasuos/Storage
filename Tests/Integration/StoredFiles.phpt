<?php
declare(strict_types = 1);
/**
* @testCase
* @phpVersion > 7.1
*/
namespace Dasuos\Tests\Integration;

use Tester\{TestCase, Assert};
use Dasuos\{Tests, Storage};

require __DIR__ . '/../bootstrap.php';

final class StoredFiles extends TestCase {

	public function testDeletedFileInDirectory() {
		Assert::noError(
			function() {
				$path = (new Tests\TestCase\PngImage(
					__DIR__ . '/../temp/StoredFiles', 800, 600
				))->path();
				(new Storage\StoredFiles(
					new Storage\FakePath
				))->delete($path);
			}
		);
	}

	public function testDeletingNonexistentFile() {
		Assert::exception(
			function() {
				$path = new Storage\FilePath('fake/directory');
				(new Storage\StoredFiles($path))
					->delete('invalid/path/to/file');
			},
			\UnexpectedValueException::class,
			'Directory path is invalid'
		);
	}
}

(new StoredFiles())->run();