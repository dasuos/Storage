<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Dasuos\Tests\Integration;

use Tester\{TestCase, Assert};
use Dasuos\{Tests, Storage, Tests\TestCase\PngImage};

require __DIR__ . '/../bootstrap.php';

final class ImageFiles extends TestCase {

	private $image;

	public function setup() {
		parent::setup();
		$this->image = (new PngImage(800, 600))->path();
	}

	public function testUploadedImageWithExceedingDimension() {
		Assert::exception(
			function() {
				(new Storage\ImageFiles(
					new Storage\FakeFiles, new Storage\FakePath, 500, 500
				))->upload(
					'fakeName', $this->image, 1900000, UPLOAD_ERR_OK
				);
			},
			Storage\ImageUploadException::class,
			'Image file exceeds dimensions'
		);
	}

	public function testUploadedImageWithValidDimension() {
		Assert::noError(
			function() {
				(new Storage\ImageFiles(
					new Storage\FakeFiles, new Storage\FakePath, 1000, 1000
				))->upload(
					'fakeName', $this->image, 1900000, UPLOAD_ERR_OK
				);
			}
		);
	}

	public function testInvalidUploadedImage() {
		Assert::exception(
			function() {
				(new Storage\ImageFiles(
					new Storage\FakeFiles, new Storage\FakePath, 500, 500
				))->upload(
					'fakeName', 'invalidImage', 1900000, UPLOAD_ERR_OK
				);
			},
			Storage\ImageUploadException::class,
			'Image file is unreadable or does not have supporting format'
		);
	}
}

(new ImageFiles())->run();