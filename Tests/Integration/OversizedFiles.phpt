<?php
declare(strict_types = 1);

namespace Dasuos\Storage\Integration;

use Dasuos\Storage;
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 * @phpVersion > 7.1
 */
final class OversizedFiles extends Tester\TestCase {

	public function allowedSizes() {
		return [
			['2B', 1],
			['2kB', 100],
			['2MB', 1600000],
			['2GB', 1600000000],
			['2TB', 1600000000000],
		];
	}

	public function exceedingSizes() {
		return [
			['2B', 3],
			['2kB', 2001],
			['2MB', 2000001],
			['2GB', 3000000000],
			['2TB', 2000000000001],
		];
	}

	public function unexpectedSizeFormats() {
		return [
			['2PB', 1600000],
			['2 B', 1600000],
			['Nonsense123', 1600000],
		];
	}


	/**
	 * @dataProvider allowedSizes
	 */
	public function testSavingFileWithAllowedSize(
		string $allowedSize,
		int $size
	) {
		Assert::noError(
			function() use ($allowedSize, $size) {
				$path = new Storage\FakePath;
				(new Storage\OversizedFiles(
					new Storage\FakeFiles($path),
					$allowedSize
				))->save(
					'fakeName',
					'fakeTmp',
					$size,
					UPLOAD_ERR_OK
				);
			}
		);
	}

	/**
	 * @dataProvider exceedingSizes
	 */
	public function testSavingFileWithExceedingSize(
		string $allowedSize,
		int $exceedingSize
	) {
		Assert::exception(
			function() use ($allowedSize, $exceedingSize) {
				$path = new Storage\FakePath;
				(new Storage\OversizedFiles(
					new Storage\FakeFiles($path),
					$allowedSize
				))->save(
					'fakeName',
					'fakeTmp',
					$exceedingSize,
					UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'Given file exceeds maximum allowed size'
		);
	}

	/**
	 * @dataProvider unexpectedSizeFormats
	 */
	public function testSavingFileWithUnexpectedSizeFormat(
		string $unexpectedSize,
		int $size
	) {
		Assert::exception(
			function() use ($unexpectedSize, $size) {
				$path = new Storage\FakePath;
				(new Storage\OversizedFiles(
					new Storage\FakeFiles($path),
					$unexpectedSize
				))->save(
					'fakeName',
					'fakeTmp',
					$size,
					UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'Expected string value in byte format'
		);
	}
}

(new OversizedFiles)->run();