<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Dasuos\Tests\Integration;

use Tester\{TestCase, Assert, FileMock};
use Dasuos\{Tests, Storage, Tests\TestCase\PngImage};

require __DIR__ . '/../bootstrap.php';

final class StrictFiles extends TestCase {

	private const VALID_SIZE = 1900000;
	private const EXCEEDING_SIZE = 3000000;

	private $image;

	public function setup() {
		parent::setup();
		$this->image = (new PngImage(800, 600))->path();
	}

	public function testUnsuccessfulUpload() {
		Assert::exception(
			function() {
				(new Storage\StrictFiles(
					new Storage\StoredFiles(new Storage\FakePath),
					new Storage\FakePath,
					['image/gif', 'image/png', 'image/jpeg']
				))->upload(
					'fakeName',
					'fakeTmp',
					self::VALID_SIZE,
					UPLOAD_ERR_CANT_WRITE
				);
			}, Storage\FileUploadException::class
		);
	}

	public function testExistingFileInUpload() {
		Assert::exception(
			function() {
				$mock = FileMock::create('data', 'txt');
				(new Storage\StrictFiles(
					new Storage\StoredFiles(new Storage\FakePath),
					new Storage\FakePath,
					['image/gif', 'image/png', 'image/jpeg']
				))->upload(
					$mock, $mock, self::VALID_SIZE, UPLOAD_ERR_OK
				);
			}, Storage\FileUploadException::class
		);
	}

	public function testExceedingSizeInUpload() {
		Assert::exception(
			function() {
				(new Storage\StrictFiles(
					new Storage\StoredFiles(new Storage\FakePath),
					new Storage\FakePath,
					['image/gif', 'image/png', 'image/jpeg']
				))->upload(
					'fakeName', 'fakeTmp', self::EXCEEDING_SIZE, UPLOAD_ERR_OK
				);
			}, Storage\FileUploadException::class
		);
	}

	public function testFileWithProhibitExtensionInUpload() {
		Assert::exception(
			function() {
				(new Storage\StrictFiles(
					new Storage\StoredFiles(new Storage\FakePath),
					new Storage\FakePath,
					['image/jpeg', 'image/gif']
				))->upload(
					'fakeName', $this->image, self::VALID_SIZE, UPLOAD_ERR_OK
				);
			}, Storage\FileUploadException::class
		);
	}

	public function testFileWithValidExtensionInUpload() {
		Assert::noError(
			function() {
				(new Storage\StrictFiles(
					new Storage\StoredFiles(new Storage\FakePath),
					new Storage\FakePath,
					['image/png']
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
						new Storage\FakePath,
						1000, 1000
					),
					new Storage\FakePath,
					['image/jpeg']
				))->upload(
					'fakeName', $this->image, self::VALID_SIZE, UPLOAD_ERR_OK
				);
			}, Storage\FileUploadException::class
		);
	}

	public function testImageWithValidExtensionInUpload() {
		Assert::noError(
			function() {
				(new Storage\StrictFiles(
					new Storage\ImageFiles(
						new Storage\StoredFiles(new Storage\FakePath),
						new Storage\FakePath,
						1000, 1000
					),
					new Storage\FakePath,
					['image/png']
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
					['image/jpeg', 'image/gif']
				))->delete('invalid/path/to/file');
			}, Storage\FileDeletionException::class
		);
	}
}

(new StrictFiles())->run();