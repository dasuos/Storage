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
final class MeasuredImages extends Tester\TestCase {

	private const TEMPORARY_IMAGE_DIRECTORY = __DIR__ .
		'/../Temp/ExceedingImages';

	public function setup() {
		parent::setup();
		Tester\Environment::lock('ExceedingImages', __DIR__ . '/../Temp/Locks');
	}

	public function testSavingImageWithExceedingDimension() {
		Assert::exception(
			function() {
				(new Storage\MeasuredImages(
					new Storage\FakeFiles,
					800,
					600
				))->save(
					'fakeName',
					(new Storage\Misc\PngImage(self::TEMPORARY_IMAGE_DIRECTORY, 2000, 2000))
						->path(),
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
					new Storage\FakeFiles,
					2000,
					2000
				))->save(
					'fakeName',
					(new Storage\Misc\PngImage(self::TEMPORARY_IMAGE_DIRECTORY, 800, 600))
						->path(),
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
					new Storage\UploadedFiles($path),
					800,
					600
				))->save(
					'fakeName',
					'invalidImage',
					1900000,
					UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'Image is unreadable or does not have supporting format'
		);
	}
}

(new MeasuredImages)->run();