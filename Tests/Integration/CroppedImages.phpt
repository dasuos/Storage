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

	private $cropped;
	private $temporary;

	public function setup() {
		parent::setup();
		Environment::lock('CroppedImages', __DIR__ . '/../Temp');
		$this->temporary = __DIR__ . '/../Temp/CroppedImages';
		$this->cropped = __DIR__ . '/../TestCase/CroppedImages/Edited';
		(new CopiedFiles(
			__DIR__ . '/../TestCase/CroppedImages/Unedited', $this->temporary
		))->copy();
	}

	public function testCroppingJpegImage() {
		$path = $this->temporary . '/my_face.jpg';

		(new Storage\CroppedImages(
			new Storage\FakeFiles,
			new Storage\FilePath(dirname($path)),
			500, 500
		))->save(basename($path), $path, filesize($path), UPLOAD_ERR_OK);

		Assert::same(
			file_get_contents($path),
			file_get_contents($this->cropped . '/my_face.jpg')
		);
	}

	public function testCroppingPngImage() {
		$path = $this->temporary . '/penguin.png';

		(new Storage\CroppedImages(
			new Storage\FakeFiles,
			new Storage\FilePath(dirname($path)),
			200, 200
		))->save(basename($path), $path, filesize($path), UPLOAD_ERR_OK);

		Assert::same(
			file_get_contents($path),
			file_get_contents($this->cropped . '/penguin.png')
		);
	}

	public function testCroppingGifImage() {
		$path = $this->temporary . '/phphant.gif';

		(new Storage\CroppedImages(
			new Storage\FakeFiles,
			new Storage\FilePath(dirname($path)),
			150, 150
		))->save(basename($path), $path, filesize($path), UPLOAD_ERR_OK);

		Assert::same(
			file_get_contents($path),
			file_get_contents($this->cropped . '/phphant.gif')
		);
	}

	public function testCroppingWiderImage() {
		$path = $this->temporary . '/my_face.jpg';

		(new Storage\CroppedImages(
			new Storage\FakeFiles,
			new Storage\FilePath(dirname($path)),
			500, 300
		))->save(basename($path), $path, filesize($path), UPLOAD_ERR_OK);

		Assert::same(
			file_get_contents($path),
			file_get_contents($this->cropped . '/my_face_wider.jpg')
		);
	}

	public function testCroppingNarrowerImage() {
		$path = $this->temporary . '/my_face.jpg';

		(new Storage\CroppedImages(
			new Storage\FakeFiles,
			new Storage\FilePath(dirname($path)),
			300, 500
		))->save(basename($path), $path, filesize($path), UPLOAD_ERR_OK);

		Assert::same(
			file_get_contents($path),
			file_get_contents($this->cropped . '/my_face_narrower.jpg')
		);
	}

	public function testCroppingExceedingThumbnail() {
		Assert::exception(
			function() {
				$path = $this->temporary . '/my_face.jpg';

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
				$path = $this->temporary . '/php_icon.svg';

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