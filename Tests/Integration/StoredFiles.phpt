<?php
declare(strict_types = 1);
/**
* @testCase
* @phpVersion > 7.1
*/
namespace Dasuos\Tests\Integration;

use Tester\{TestCase, Assert, FileMock};
use Dasuos\{Tests, Storage};

require __DIR__ . '/../bootstrap.php';

final class StoredFiles extends TestCase {

	public function testDeletedFileInDirectory() {
		$mock = FileMock::create('data', 'txt');
		(new Storage\StoredFiles(
			new Storage\FakePath
		))->delete($mock);
		Assert::false(file_exists($mock));
	}
}

(new StoredFiles())->run();