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
	private const EXCEEDING_SIZE = 3000000;

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
				$path = new Storage\FilePath('fake/directory');
				(new Storage\StrictFiles(
					new Storage\StoredFiles($path),
					$path,
					new Storage\FileExtensions(
						['image/gif', 'image/png', 'image/jpeg']
					)
				))->upload(
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
				$path = new Storage\FilePath(__DIR__ . '/../temp/StrictFiles');
				var_dump(basename($this->image));
				(new Storage\StrictFiles(
					new Storage\StoredFiles($path),
					$path,
					new Storage\FileExtensions(
						['image/gif', 'image/png', 'image/jpeg']
					)
				))->upload(
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
				$path = new Storage\FilePath('fake/directory');
				(new Storage\StrictFiles(
					new Storage\StoredFiles($path),
					$path,
					new Storage\FileExtensions(
						['image/gif', 'image/png', 'image/jpeg']
					)
				))->upload(
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
				$path = new Storage\FilePath('fake/directory');
				(new Storage\StrictFiles(
					new Storage\StoredFiles($path),
					$path,
					new Storage\FileExtensions(['image/gif', 'image/jpeg'])
				))->upload(
					'fakeName', $this->image, self::VALID_SIZE, UPLOAD_ERR_OK
				);
			}, \UnexpectedValueException::class
		);
	}

	public function testFileWithValidExtensionInUpload() {
		Assert::exception(
			function() {
				$path = new Storage\FilePath(__DIR__);
				(new Storage\StrictFiles(
					new Storage\StoredFiles($path),
					$path,
					new Storage\FileExtensions(['image/png'])
				))->upload(
					'fakeName', $this->image, self::VALID_SIZE, UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'File must be uploaded via HTTP POST'
		);
	}

	public function testImageWithInvalidExtensionInUpload() {
		Assert::exception(
			function() {
				$path = new Storage\FilePath('fake/directory');
				(new Storage\StrictFiles(
					new Storage\ExceedingImages(
						new Storage\StoredFiles($path),
						new Storage\InformativeImage,
						2000, 2000
					),
					$path,
					new Storage\FileExtensions(['image/jpeg'])
				))->upload(
					'fakeName', $this->image, self::VALID_SIZE, UPLOAD_ERR_OK
				);
			}, \UnexpectedValueException::class
		);
	}

	public function testImageWithValidExtensionInUpload() {
		Assert::exception(
			function() {
				$path = new Storage\FilePath('fake/directory');
				(new Storage\StrictFiles(
					new Storage\ExceedingImages(
						new Storage\StoredFiles($path),
						new Storage\InformativeImage,
						2000, 2000
					),
					$path,
					new Storage\FileExtensions(['image/png'])
				))->upload(
					'fakeName', $this->image, self::VALID_SIZE, UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'Directory path is invalid'
		);
	}
}

(new StrictFiles())->run();