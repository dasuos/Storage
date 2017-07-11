<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Dasuos\Tests\Integration;

use Tester\{TestCase, Assert, FileMock, Environment};
use Dasuos\{Tests, Storage, Tests\TestCase\PngImage};

require __DIR__ . '/../bootstrap.php';

final class StrictFiles extends TestCase {

	private const VALID_SIZE = 1900000;
	private const EXCEEDING_SIZE = 3000000;

	private $image;

	public function setup() {
		parent::setup();
		Environment::lock('StrictFiles', __DIR__ . '/../temp');
		$this->image = (new PngImage(
			__DIR__ . '/../temp/StrictFiles',800, 600
		))->path();
	}

	public function testUnsuccessfulUpload() {
		Assert::exception(
			function() {
				(new Storage\StrictFiles(
					new Storage\StoredFiles(new Storage\FakePath),
					new Storage\FakePath,
					new Storage\FileExtensions(
						['image/gif', 'image/png', 'image/jpeg']
					)
				))->upload(
					'fakeName',
					'fakeTmp',
					self::VALID_SIZE,
					UPLOAD_ERR_CANT_WRITE
				);
			}, \UnexpectedValueException::class
		);
	}

	public function testExistingFileInUpload() {
		Assert::exception(
			function() {
				$mock = FileMock::create('data', 'txt');
				(new Storage\StrictFiles(
					new Storage\StoredFiles(new Storage\FakePath),
					new Storage\FakePath,
					new Storage\FileExtensions(
						['image/gif', 'image/png', 'image/jpeg']
					)
				))->upload(
					$mock, $mock, self::VALID_SIZE, UPLOAD_ERR_OK
				);
			}, \UnexpectedValueException::class
		);
	}

	public function testExceedingSizeInUpload() {
		Assert::exception(
			function() {
				(new Storage\StrictFiles(
					new Storage\StoredFiles(new Storage\FakePath),
					new Storage\FakePath,
					new Storage\FileExtensions(
						['image/gif', 'image/png', 'image/jpeg']
					)
				))->upload(
					'fakeName', 'fakeTmp', self::EXCEEDING_SIZE, UPLOAD_ERR_OK
				);
			}, \UnexpectedValueException::class
		);
	}

	public function testFileWithProhibitExtensionInUpload() {
		Assert::exception(
			function() {
				(new Storage\StrictFiles(
					new Storage\StoredFiles(new Storage\FakePath),
					new Storage\FakePath,
					new Storage\FileExtensions(
						['image/gif', 'image/jpeg']
					)
				))->upload(
					'fakeName', $this->image, self::VALID_SIZE, UPLOAD_ERR_OK
				);
			}, \UnexpectedValueException::class
		);
	}

	public function testFileWithValidExtensionInUpload() {
		Assert::noError(
			function() {
				(new Storage\StrictFiles(
					new Storage\StoredFiles(new Storage\FakePath),
					new Storage\FakePath,
					new Storage\FileExtensions(
						['image/png']
					)
				))->upload(
					'fakeName', $this->image, self::VALID_SIZE, UPLOAD_ERR_OK
				);
			}
		);
	}

	public function testImageWithInvalidExtensionInUpload() {
		Assert::exception(
			function() {
				(new Storage\StrictFiles(
					new Storage\ImageFiles(
						new Storage\StoredFiles(new Storage\FakePath),
						new Storage\InformativeImage
					),
					new Storage\FakePath,
					new Storage\FileExtensions(
						['image/jpeg']
					)
				))->upload(
					'fakeName', $this->image, self::VALID_SIZE, UPLOAD_ERR_OK
				);
			}, \UnexpectedValueException::class
		);
	}

	public function testImageWithValidExtensionInUpload() {
		Assert::noError(
			function() {
				(new Storage\StrictFiles(
					new Storage\ImageFiles(
						new Storage\StoredFiles(new Storage\FakePath),
						new Storage\InformativeImage
					),
					new Storage\FakePath,
					new Storage\FileExtensions(
						['image/png']
					)
				))->upload(
					'fakeName', $this->image, self::VALID_SIZE, UPLOAD_ERR_OK
				);
			}
		);
	}

	public function testDeletingNonexistentFile() {
		Assert::exception(
			function() {
				(new Storage\StrictFiles(
					new Storage\StoredFiles(new Storage\FakePath),
					new Storage\FakePath,
					new Storage\FileExtensions(
						['image/gif', 'image/png', 'image/jpeg']
					)
				))->delete('invalid/path/to/file');
			}, \UnexpectedValueException::class
		);
	}
}

(new StrictFiles())->run();