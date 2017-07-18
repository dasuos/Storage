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

final class ExceedingImages extends TestCase {

	private $directory;

	public function setup() {
		parent::setup();
		Environment::lock('ExceedingImages', __DIR__ . '/../Temp');
		$this->directory = __DIR__ . '/../Temp/ExceedingImages';
	}

	public function testUploadedImageWithExceedingDimension() {
		Assert::exception(
			function() {
				(new Storage\ExceedingImages(
					new Storage\FakeFiles,
					new Storage\InformativeImage,
					800, 600
				))->save(
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
		Assert::noError(
			function() {
				(new Storage\ExceedingImages(
					new Storage\FakeFiles,
					new Storage\InformativeImage,
					2000, 2000
				))->save(
					'fakeName',
					(new PngImage($this->directory, 800, 600))->path(),
					1900000,
					UPLOAD_ERR_OK
				);
			}
		);
	}

	public function testInvalidUploadedImage() {
		Assert::exception(
			function() {
				$path = new Storage\FilePath('fake/directory');
				(new Storage\ExceedingImages(
					new Storage\UploadedFiles($path),
					new Storage\InformativeImage,
					800, 600
				))->save(
					'fakeName', 'invalidImage', 1900000, UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'Image file is unreadable or does not have supporting format'
		);
	}

	public function testInvalidPngImageToRotate() {
		Assert::noError(
			function() {
				$path = new Storage\FakePath;
				$image = new Storage\InformativeImage;

				(new Storage\RotatedImages(
					new Storage\ExceedingImages(
						new Storage\FakeFiles,
						$image, 2000, 2000
					), $image, $path
				))->save(
					'fakeName',
					(new PngImage($this->directory, 800, 600))->path(),
					1900000,
					UPLOAD_ERR_OK
				);
			}
		);
	}

}

(new ExceedingImages())->run();