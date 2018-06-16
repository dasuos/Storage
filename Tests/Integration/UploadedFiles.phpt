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
final class UploadedFiles extends Tester\TestCase {

	private $file;

	public function setup() {
		parent::setup();
		Tester\Environment::lock('UploadedFiles', __DIR__ . '/../Temp/Locks');
		$this->file = (new Storage\Misc\PngImage(
			__DIR__ . '/../Temp/UploadedFiles',
			800,
			600
		))->path();
	}

	public function testDeletingFileInDirectory() {
		Assert::noError(
			function() {
				$path = (new Storage\Misc\PngImage(
					__DIR__ . '/../Temp/UploadedFiles',
					800,
					600
				))->path();
				(new Storage\UploadedFiles(
					new Storage\FakePath
				))->delete($path);
			}
		);
	}

	public function testDeletingNonexistentFile() {
		Assert::exception(
			function() {
				$path = new Storage\FilePath('fake/directory');
				(new Storage\UploadedFiles($path))
					->delete('invalid/path/to/file');
			},
			\UnexpectedValueException::class,
			'Given file path does not exist'
		);
	}

	public function testSavingFileWithoutHttpPostMechanism() {
		Assert::exception(
			function() {
				(new Storage\UploadedFiles(
					new Storage\FilePath(dirname($this->file))
				))->save(
					basename($this->file),
					$this->file,
					filesize($this->file),
					UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'Given file cannot be uploaded'
		);
	}
}

(new UploadedFiles)->run();