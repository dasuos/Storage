<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Dasuos\Tests\Integration;

use Tester\{TestCase, Assert, Environment};
use Dasuos\{Tests, Storage, Tests\TestCase\PngImage};

require __DIR__ . '/../bootstrap.php';

final class StrictFiles extends TestCase {

	private const VALID_SIZE = 1900000;
	private const EXCEEDING_SIZE = 7000000;

	private $image;

	public function setup() {
		parent::setup();
		Environment::lock('StrictFiles', __DIR__ . '/../Temp');
		$this->image = (new PngImage(
			__DIR__ . '/../Temp/StrictFiles',800, 600
		))->path();
	}

	public function testUnsuccessfulUpload() {
		Assert::exception(
			function() {
				$path = new Storage\FakePath;
				(new Storage\StrictFiles(
					new Storage\FakeFiles,
					$path,
					new Storage\FileExtensions(
						['image/gif', 'image/png', 'image/jpeg']
					)
				))->save(
					'fakeName',
					'fakeTmp',
					self::VALID_SIZE,
					UPLOAD_ERR_CANT_WRITE
				);
			},
			\UnexpectedValueException::class
		);
	}

	public function testExistingFileInUpload() {
		Assert::exception(
			function() {
				(new Storage\StrictFiles(
					new Storage\FakeFiles,
					new Storage\FilePath(dirname($this->image)),
					new Storage\FileExtensions(
						['image/gif', 'image/png', 'image/jpeg']
					)
				))->save(
					basename($this->image),
					$this->image,
					self::VALID_SIZE,
					UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class
		);
	}

	public function testExceedingSizeInUpload() {
		Assert::exception(
			function() {
				$path = new Storage\FakePath;
				(new Storage\StrictFiles(
					new Storage\FakeFiles,
					$path,
					new Storage\FileExtensions(
						['image/gif', 'image/png', 'image/jpeg']
					)
				))->save(
					'fakeName',
					$this->image,
					self::EXCEEDING_SIZE,
					UPLOAD_ERR_OK
				);
			}, \UnexpectedValueException::class
		);
	}

	public function testFileWithProhibitExtensionInUpload() {
		Assert::exception(
			function() {
				$path = new Storage\FakePath;
				(new Storage\StrictFiles(
					new Storage\FakeFiles,
					$path,
					new Storage\FileExtensions(['image/gif', 'image/jpeg'])
				))->save(
					'fakeName', $this->image, self::VALID_SIZE, UPLOAD_ERR_OK
				);
			}, \UnexpectedValueException::class
		);
	}

	public function testFileWithValidExtensionInUpload() {
		Assert::noError(
			function() {
				$path = new Storage\FakePath;
				(new Storage\StrictFiles(
					new Storage\FakeFiles,
					$path,
					new Storage\FileExtensions(['image/png'])
				))->save(
					'fakeName', $this->image, self::VALID_SIZE, UPLOAD_ERR_OK
				);
			}
		);
	}
}

(new StrictFiles())->run();