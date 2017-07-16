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

	public function testIntegerAllowedSize() {
		Assert::noError(
			function() {
				$path = new Storage\FakePath;
				(new Storage\OversizedFiles(
					new Storage\FakeFiles($path), 2000000
				))->save(
					'fakeName', 'fakeTmp', 1600000, UPLOAD_ERR_OK
				);
			}
		);
	}

	public function testIntegerExceedingSize() {
		Assert::exception(
			function() {
				$path = new Storage\FakePath;
				(new Storage\OversizedFiles(
					new Storage\FakeFiles($path), 2000000
				))->save(
					'fakeName', 'fakeTmp', 3000000, UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'Given file exceeds maximum allowed size'
		);
	}

	public function testByteFormatAllowedSize() {
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

	public function testByteFormatExceedingSize() {
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

	public function testKiloByteFormatAllowedSize() {
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

	public function testKiloByteFormatExceedingSize() {
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

	public function testMegaByteFormatAllowedSize() {
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

	public function testMegaByteFormatExceedingSize() {
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

	public function testGigaByteFormatAllowedSize() {
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

	public function testGigaByteFormatExceedingSize() {
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

	public function testTeraByteFormatAllowedSize() {
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

	public function testTeraByteFormatExceedingSize() {
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

	public function testUndeclaredByteFormatAllowedSize() {
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
			'Expected integer or string value in byte format'
		);
	}

	public function testByteFormatWithSpace() {
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
			'Expected integer or string value in byte format'
		);
	}

	public function testRandomStringInsteadByteFormat() {
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
			'Expected integer or string value in byte format'
		);
	}
}

(new OversizedFiles())->run();