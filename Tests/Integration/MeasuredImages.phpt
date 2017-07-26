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

final class MeasuredImages extends TestCase {

	private $directory;

	public function setup() {
		parent::setup();
		Environment::lock('ExceedingImages', __DIR__ . '/../Temp');
		$this->directory = __DIR__ . '/../Temp/ExceedingImages';
	}

	public function testSavingImageWithExceedingDimension() {
		Assert::exception(
			function() {
				(new Storage\MeasuredImages(
					new Storage\FakeFiles, 800, 600
				))->save(
					'fakeName',
					(new PngImage($this->directory, 2000, 2000))->path(),
					1900000,
					UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'Image exceeds maximum dimensions'
		);
	}

	public function testSavingImageWithValidDimension() {
		Assert::noError(
			function() {
				(new Storage\MeasuredImages(
					new Storage\FakeFiles, 2000, 2000
				))->save(
					'fakeName',
					(new PngImage($this->directory, 800, 600))->path(),
					1900000,
					UPLOAD_ERR_OK
				);
			}
		);
	}

	public function testSavingInvalidUploadedImage() {
		Assert::exception(
			function() {
				$path = new Storage\FilePath('fake/directory');
				(new Storage\MeasuredImages(
					new Storage\UploadedFiles($path), 800, 600
				))->save(
					'fakeName', 'invalidImage', 1900000, UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'Image is unreadable or does not have supporting format'
		);
	}

	public function testSavingInvalidPngImageToRotate() {
		Assert::noError(
			function() {
				$path = new Storage\FakePath;

				(new Storage\RotatedImages(
					new Storage\MeasuredImages(
						new Storage\FakeFiles, 2000, 2000
					), $path
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

(new MeasuredImages())->run();