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

final class OversizedFiles extends TestCase {

	public function testSavingFileWithAllowedByteSize() {
		Assert::noError(
			function() {
				$path = new Storage\FakePath;
				(new Storage\OversizedFiles(
					new Storage\FakeFiles($path), '2B'
				))->save(
					'fakeName', 'fakeTmp', 1, UPLOAD_ERR_OK
				);
			}
		);
	}

	public function testSavingFileWithExceedingByteSize() {
		Assert::exception(
			function() {
				$path = new Storage\FakePath;
				(new Storage\OversizedFiles(
					new Storage\FakeFiles($path), '2B'
				))->save(
					'fakeName', 'fakeTmp', 3, UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'Given file exceeds maximum allowed size'
		);
	}

	public function testSavingFileWithAllowedKiloByteSize() {
		Assert::noError(
			function() {
				$path = new Storage\FakePath;
				(new Storage\OversizedFiles(
					new Storage\FakeFiles($path), '2kB'
				))->save(
					'fakeName', 'fakeTmp', 100, UPLOAD_ERR_OK
				);
			}
		);
	}

	public function testSavingFileWithExceedingKiloByteSize() {
		Assert::exception(
			function() {
				$path = new Storage\FakePath;
				(new Storage\OversizedFiles(
					new Storage\FakeFiles($path), '2kB'
				))->save(
					'fakeName', 'fakeTmp', 2001, UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'Given file exceeds maximum allowed size'
		);
	}

	public function testSavingFileWithAllowedMegaByteSize() {
		Assert::noError(
			function() {
				$path = new Storage\FakePath;
				(new Storage\OversizedFiles(
					new Storage\FakeFiles($path), '2MB'
				))->save(
					'fakeName', 'fakeTmp', 1600000, UPLOAD_ERR_OK
				);
			}
		);
	}

	public function testSavingFileWithExceedingMegaByteSize() {
		Assert::exception(
			function() {
				$path = new Storage\FakePath;
				(new Storage\OversizedFiles(
					new Storage\FakeFiles($path), '2MB'
				))->save(
					'fakeName', 'fakeTmp', 2000001, UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'Given file exceeds maximum allowed size'
		);
	}

	public function testSavingFileWithAllowedGigaByteSize() {
		Assert::noError(
			function() {
				$path = new Storage\FakePath;
				(new Storage\OversizedFiles(
					new Storage\FakeFiles($path), '2GB'
				))->save(
					'fakeName', 'fakeTmp', 1600000000, UPLOAD_ERR_OK
				);
			}
		);
	}

	public function testSavingFileWithExceedingGigaByteSize() {
		Assert::exception(
			function() {
				$path = new Storage\FakePath;
				(new Storage\OversizedFiles(
					new Storage\FakeFiles($path), '2GB'
				))->save(
					'fakeName', 'fakeTmp', 3000000000, UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'Given file exceeds maximum allowed size'
		);
	}

	public function testSavingFileWithAllowedTeraByteSize() {
		Assert::noError(
			function() {
				$path = new Storage\FakePath;
				(new Storage\OversizedFiles(
					new Storage\FakeFiles($path), '2TB'
				))->save(
					'fakeName', 'fakeTmp', 1600000000000, UPLOAD_ERR_OK
				);
			}
		);
	}

	public function testSavingFileWithExceedingTeraByteSize() {
		Assert::exception(
			function() {
				$path = new Storage\FakePath;
				(new Storage\OversizedFiles(
					new Storage\FakeFiles($path), '2TB'
				))->save(
					'fakeName', 'fakeTmp', 2000000000001, UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'Given file exceeds maximum allowed size'
		);
	}

	public function testSavingFileWithUndeclaredByteSize() {
		Assert::exception(
			function() {
				$path = new Storage\FakePath;
				(new Storage\OversizedFiles(
					new Storage\FakeFiles($path), '2PB'
				))->save(
					'fakeName', 'fakeTmp', 1600000, UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'Expected string value in byte format'
		);
	}

	public function testSavingFileWithByteSizeWithSpace() {
		Assert::exception(
			function() {
				$path = new Storage\FakePath;
				(new Storage\OversizedFiles(
					new Storage\FakeFiles($path), '2 B'
				))->save(
					'fakeName', 'fakeTmp', 1600000, UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'Expected string value in byte format'
		);
	}

	public function testSavingFileWithRandomStringInsteadByteSize() {
		Assert::exception(
			function() {
				$path = new Storage\FakePath;
				(new Storage\OversizedFiles(
					new Storage\FakeFiles($path), 'Nonsense123'
				))->save(
					'fakeName', 'fakeTmp', 1600000, UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'Expected string value in byte format'
		);
	}
}

(new OversizedFiles())->run();