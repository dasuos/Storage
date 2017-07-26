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

final class UploadedFiles extends TestCase {

	private $file;

	public function setup() {
		parent::setup();
		Environment::lock('UploadedFiles', __DIR__ . '/../Temp');
		$this->file = (new PngImage(
			__DIR__ . '/../Temp/UploadedFiles', 800, 600
		))->path();
	}

	public function testDeletingFileInDirectory() {
		Assert::noError(
			function() {
				$path = (new Tests\TestCase\PngImage(
					__DIR__ . '/../Temp/UploadedFiles', 800, 600
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
			'Given file does not exist and cannot be deleted'
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

(new UploadedFiles())->run();