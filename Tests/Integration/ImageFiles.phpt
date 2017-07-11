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

final class ImageFiles extends TestCase {

	private $directory;

	public function setup() {
		parent::setup();
		Environment::lock('ImageFiles', __DIR__ . '/../temp');
		$this->directory = __DIR__ . '/../temp/ImageFiles';
	}

	public function testUploadedImageWithExceedingDimension() {
		$image = new PngImage($this->directory, 2500, 2500);
		Assert::exception(
			function() use ($image) {
				(new Storage\ImageFiles(
					new Storage\StoredFiles(new Storage\FakePath),
					new Storage\InformativeImage
				))->upload(
					'fakeName',
					$image->path(),
					1900000,
					UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'Image file exceeds maximum dimensions'
		);
	}

	public function testUploadedImageWithValidDimension() {
		$image = new PngImage($this->directory, 800, 600);
		Assert::noError(
			function() use ($image) {
				(new Storage\ImageFiles(
					new Storage\StoredFiles(new Storage\FakePath),
					new Storage\InformativeImage
				))->upload(
					'fakeName',
					$image->path(),
					1900000,
					UPLOAD_ERR_OK
				);
			}
		);
	}

	public function testInvalidUploadedImage() {
		Assert::exception(
			function() {
				(new Storage\ImageFiles(
					new Storage\StoredFiles(new Storage\FakePath),
					new Storage\InformativeImage
				))->upload(
					'fakeName', 'invalidImage', 1900000, UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'Image file is unreadable or does not have supporting format'
		);
	}
}

(new ImageFiles())->run();