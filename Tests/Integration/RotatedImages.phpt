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

final class RotatedImages extends TestCase {

	private $rotated;
	private $temporary;

	public function setup() {
		parent::setup();
		Environment::lock('RotatedImages', __DIR__ . '/../Temp');
		$this->temporary = __DIR__ . '/../Temp/RotatedImages';
		$this->rotated = __DIR__ . '/../TestCase/RotatedImages/Edited';
		(new CopiedFiles(
			__DIR__ . '/../TestCase/RotatedImages/Unedited', $this->temporary
		))->copy();
	}

	public function testDownRotatedImageModification() {
		$path = $this->temporary . '/down.jpg';

		(new Storage\RotatedImages(
			new Storage\FakeFiles,
			new Storage\FilePath(dirname($path)),
			new Storage\InformativeImage
		))->save(basename($path), $path, filesize($path), UPLOAD_ERR_OK);

		Assert::same(
			file_get_contents($path),
			file_get_contents($this->rotated . '/down.jpg')
		);
	}

	public function testDownMirroredRotatedImageModification() {
		$path = $this->temporary . '/down-mirrored.jpg';

		(new Storage\RotatedImages(
			new Storage\FakeFiles,
			new Storage\FilePath(dirname($path)),
			new Storage\InformativeImage
		))->save(basename($path), $path, filesize($path), UPLOAD_ERR_OK);

		Assert::same(
			file_get_contents($path),
			file_get_contents(
				$this->rotated . '/down-mirrored.jpg'
			)
		);
	}

	public function testLeftRotatedImageModification() {
		$path = $this->temporary . '/left.jpg';

		(new Storage\RotatedImages(
			new Storage\FakeFiles,
			new Storage\FilePath(dirname($path)),
			new Storage\InformativeImage
		))->save(basename($path), $path, filesize($path), UPLOAD_ERR_OK);

		Assert::same(
			file_get_contents($path),
			file_get_contents($this->rotated . '/left.jpg')
		);
	}

	public function testLeftMirroredRotatedImageModification() {
		$path = $this->temporary . '/left-mirrored.jpg';

		(new Storage\RotatedImages(
			new Storage\FakeFiles,
			new Storage\FilePath(dirname($path)),
			new Storage\InformativeImage
		))->save(basename($path), $path, filesize($path), UPLOAD_ERR_OK);

		Assert::same(
			file_get_contents($path),
			file_get_contents(
				$this->rotated . '/left-mirrored.jpg'
			)
		);
	}

	public function testRightRotatedImageModification() {
		$path = $this->temporary . '/right.jpg';

		(new Storage\RotatedImages(
			new Storage\FakeFiles,
			new Storage\FilePath(dirname($path)),
			new Storage\InformativeImage
		))->save(basename($path), $path, filesize($path), UPLOAD_ERR_OK);

		Assert::same(
			file_get_contents($path),
			file_get_contents(
				$this->rotated . '/right.jpg'
			)
		);
	}

	public function testRightMirroredRotatedImageModification() {
		$path = $this->temporary . '/right-mirrored.jpg';

		(new Storage\RotatedImages(
			new Storage\FakeFiles,
			new Storage\FilePath(dirname($path)),
			new Storage\InformativeImage
		))->save(basename($path), $path, filesize($path), UPLOAD_ERR_OK);

		Assert::same(
			file_get_contents($path),
			file_get_contents(
				$this->rotated . '/right-mirrored.jpg'
			)
		);
	}

	public function testUpRotatedImageModification() {
		$path = $this->temporary . '/up.jpg';

		(new Storage\RotatedImages(
			new Storage\FakeFiles,
			new Storage\FilePath(dirname($path)),
			new Storage\InformativeImage
		))->save(basename($path), $path, filesize($path), UPLOAD_ERR_OK);

		Assert::same(
			file_get_contents($path),
			file_get_contents($this->rotated . '/up.jpg')
		);
	}

	public function testUpMirroredRotatedImageModification() {
		$path = $this->temporary . '/up-mirrored.jpg';

		(new Storage\RotatedImages(
			new Storage\FakeFiles,
			new Storage\FilePath(dirname($path)),
			new Storage\InformativeImage
		))->save(basename($path), $path, filesize($path), UPLOAD_ERR_OK);

		Assert::same(
			file_get_contents($path),
			file_get_contents(
				$this->rotated . '/up-mirrored.jpg'
			)
		);
	}
}

(new RotatedImages())->run();