<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Dasuos\Tests\Integration;

use Tester\{TestCase, Assert, Environment};
use Dasuos\{Tests, Storage, Tests\TestCase\CopiedFiles};

require __DIR__ . '/../bootstrap.php';

final class CroppedImages extends TestCase {

	private const CROPPED_IMAGE_PATH = __DIR__ .
		'/../TestCase/CroppedImages/Edited';
	private const TEMPORARY_IMAGE_PATH = __DIR__ . '/../Temp/CroppedImages';

	public function setup() {
		parent::setup();
		Environment::lock('CroppedImages', __DIR__ . '/../Temp');
		(new CopiedFiles(
			__DIR__ . '/../TestCase/CroppedImages/Unedited',
			self::TEMPORARY_IMAGE_PATH
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
		string $image, string $croppedImage, int $width, int $height
	) {
		$path = self::TEMPORARY_IMAGE_PATH . $image;

		(new Storage\CroppedImages(
			new Storage\FakeFiles,
			new Storage\FilePath(dirname($path)),
			$width, $height
		))->save(basename($path), $path, filesize($path), UPLOAD_ERR_OK);

		Assert::same(
			file_get_contents($path),
			file_get_contents(self::CROPPED_IMAGE_PATH . $croppedImage)
		);
	}

	public function testCroppingExceedingThumbnail() {
		Assert::exception(
			function() {
				$path = self::TEMPORARY_IMAGE_PATH . '/my_face.jpg';

				(new Storage\CroppedImages(
					new Storage\FakeFiles,
					new Storage\FilePath(dirname($path)),
					3000, 3000
				))->save(basename($path), $path, filesize($path), UPLOAD_ERR_OK);
			},
			\UnexpectedValueException::class,
			'Image does not have valid format or size'
		);
	}

	public function testCroppingProhibitFile() {
		Assert::exception(
			function() {
				$path = self::TEMPORARY_IMAGE_PATH . '/php_icon.svg';

				(new Storage\CroppedImages(
					new Storage\FakeFiles,
					new Storage\FilePath(dirname($path)),
					100, 100
				))->save(basename($path), $path, filesize($path), UPLOAD_ERR_OK);
			},
			\UnexpectedValueException::class,
			'Image is unreadable or does not have supporting format'
		);
	}
}

(new CroppedImages())->run();