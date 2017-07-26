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
	public function images() {
		return [
			['/down.jpg'],
			['/down-mirrored.jpg'],
			['/left.jpg'],
			['/left-mirrored.jpg'],
			['/right.jpg'],
			['/right-mirrored.jpg'],
			['/up.jpg'],
			['/up-mirrored.jpg'],
		];
	}

	/**
	 * @dataProvider images
	 */

	public function testSavingImageModification($file) {
		$path = $this->temporary . $file;

		(new Storage\RotatedImages(
			new Storage\FakeFiles,
			new Storage\FilePath(dirname($path))
		))->save(basename($path), $path, filesize($path), UPLOAD_ERR_OK);

		Assert::same(
			file_get_contents($path),
			file_get_contents($this->rotated . $file)
		);
	}
}

(new RotatedImages())->run();