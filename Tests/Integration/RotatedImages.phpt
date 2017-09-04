<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Dasuos\Storage\Integration;

use Dasuos\Storage;
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class RotatedImages extends Tester\TestCase {

	private const ROTATED_IMAGE_DIRECTORY = __DIR__ .
		'/../TestCase/RotatedImages/Edited';
	private const TEMPORARY_IMAGE_DIRECTORY = __DIR__ .
		'/../Temp/RotatedImages';

	public function setup() {
		parent::setup();
		Tester\Environment::lock('RotatedImages', __DIR__ . '/../Temp/Locks');
		(new Storage\TestCase\CopiedFiles(
			__DIR__ . '/../TestCase/RotatedImages/Unedited',
			self::TEMPORARY_IMAGE_DIRECTORY
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
	public function testSavingImageModification(string $file) {
		$path = self::TEMPORARY_IMAGE_DIRECTORY . $file;

		(new Storage\RotatedImages(
			new Storage\FakeFiles,
			new Storage\FilePath(dirname($path))
		))->save(basename($path), $path, filesize($path), UPLOAD_ERR_OK);

		Assert::same(
			file_get_contents($path),
			file_get_contents(self::ROTATED_IMAGE_DIRECTORY . $file)
		);
	}
}

(new RotatedImages())->run();