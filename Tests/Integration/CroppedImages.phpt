<?php
declare(strict_types = 1);

namespace Dasuos\Storage\Integration;

use Dasuos\Storage;
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 * @phpVersion < 7.2
 */

final class CroppedImages extends Tester\TestCase {

	private const CROPPED_IMAGE_DIRECTORY = __DIR__ .
	'/../Fixtures/CroppedImages/Edited';
	private const TEMPORARY_IMAGE_DIRECTORY = __DIR__ .
		'/../Temp/CroppedImages';

	public function setup() {
		parent::setup();
		Tester\Environment::lock('CroppedImages', __DIR__ . '/../Temp/Locks');
		(new Storage\Misc\CopiedFiles(
			__DIR__ . '/../Fixtures/CroppedImages/Unedited',
			self::TEMPORARY_IMAGE_DIRECTORY
		))->copy();
	}

	public function images() {
		return [
			['/my_face.jpg', '/my_face.jpg', 500, 500],
			['/penguin.png', '/penguin.png', 200, 200],
			['/phphant.gif', '/phphant.gif', 150, 150],
			['/phphant.gif', '/phphant.gif', 150, 150],
			['/my_face.jpg', '/my_face_wider.jpg', 500, 300],
			['/my_face.jpg', '/my_face_narrower.jpg', 300, 500],
		];
	}

	/**
	 * @dataProvider images
	 */
	public function testCroppingImage(
		string $image,
		string $croppedImage,
		int $width,
		int $height
	) {
		$path = self::TEMPORARY_IMAGE_DIRECTORY . $image;

		(new Storage\CroppedImages(
			new Storage\FakeFiles,
			new Storage\FilePath(dirname($path)),
			$width,
			$height
		))->save(basename($path), $path, filesize($path), UPLOAD_ERR_OK);

		\Tester\Assert::same(
			file_get_contents($path),
			file_get_contents(self::CROPPED_IMAGE_DIRECTORY . $croppedImage)
		);
	}

	public function testCroppingExceedingThumbnail() {
		\Tester\Assert::exception(
			function() {
				$path = self::TEMPORARY_IMAGE_DIRECTORY . '/my_face.jpg';

				(new Storage\CroppedImages(
					new Storage\FakeFiles,
					new Storage\FilePath(dirname($path)),
					3000,
					3000
				))->save(
					basename($path),
					$path,
					filesize($path),
					UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'Image does not have valid format or size'
		);
	}

	public function testCroppingProhibitFile() {
		Assert::exception(
			function() {
				$path = self::TEMPORARY_IMAGE_DIRECTORY . '/php_icon.svg';

				(new Storage\CroppedImages(
					new Storage\FakeFiles,
					new Storage\FilePath(dirname($path)),
					100,
					100
				))->save(
					basename($path),
					$path,
					filesize($path),
					UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'Image is unreadable or does not have supporting format'
		);
	}
}

(new CroppedImages)->run();
