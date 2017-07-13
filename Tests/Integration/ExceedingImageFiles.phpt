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

final class ExceedingImageFiles extends TestCase {

	private $directory;

	public function setup() {
		parent::setup();
		Environment::lock('ImageFiles', __DIR__ . '/../temp');
		$this->directory = __DIR__ . '/../temp/ImageFiles';
	}

	public function testUploadedImageWithExceedingDimension() {
		Assert::exception(
			function() {
				$path = new Storage\FilePath('fake/directory');
				(new Storage\ExceedingImageFiles(
					new Storage\StoredFiles($path),
					new Storage\InformativeImage,
					800, 600
				))->upload(
					'fakeName',
					(new PngImage($this->directory, 2000, 2000))->path(),
					1900000,
					UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'Image file exceeds maximum dimensions'
		);
	}

	public function testUploadedImageWithValidDimension() {
		Assert::exception(
			function() {
				$path = new Storage\FilePath('fake/directory');
				(new Storage\ExceedingImageFiles(
					new Storage\StoredFiles($path),
					new Storage\InformativeImage,
					2000, 2000
				))->upload(
					'fakeName',
					(new PngImage($this->directory, 800, 600))->path(),
					1900000,
					UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'File must be uploaded via HTTP POST'
		);
	}

	public function testInvalidUploadedImage() {
		Assert::exception(
			function() {
				$path = new Storage\FilePath('fake/directory');
				(new Storage\ExceedingImageFiles(
					new Storage\StoredFiles($path),
					new Storage\InformativeImage,
					800, 600
				))->upload(
					'fakeName', 'invalidImage', 1900000, UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'Image file is unreadable or does not have supporting format'
		);
	}

	public function testInvalidPngImageToRotate() {
		Assert::exception(
			function() {
				$path = new Storage\FilePath('fake/directory');
				$image = new Storage\InformativeImage;

				(new Storage\RotatedImageFiles(
					new Storage\ExceedingImageFiles(
						new Storage\StoredFiles($path),
						$image, 2000, 2000
					), $path, $image
				))->upload(
					'fakeName',
					(new PngImage($this->directory, 800, 600))->path(),
					1900000,
					UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'File must be uploaded via HTTP POST'
		);
	}

}

(new ExceedingImageFiles())->run();