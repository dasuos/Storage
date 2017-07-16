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

final class IdentifiedFiles extends TestCase {

	private $file;

	public function setup() {
		parent::setup();
		Environment::lock('IdentifiedFiles', __DIR__ . '/../Temp');
		$this->file = (new PngImage(
			__DIR__ . '/../Temp/IdentifiedFiles',800, 600
		))->path();
	}

	public function testFileWithProhibitFormat() {
		Assert::exception(
			function() {
				(new Storage\IdentifiedFiles(
					new Storage\FakeFiles,
					['image/gif', 'image/jpeg']
				))->save(
					basename($this->file),
					$this->file,
					filesize($this->file),
					UPLOAD_ERR_OK
				);
			},
			\UnexpectedValueException::class,
			'Given file has prohibit format'
		);
	}

	public function testFileWithValidFormat() {
		Assert::noError(
			function() {
				(new Storage\IdentifiedFiles(
					new Storage\FakeFiles,
					['image/png']
				))->save(
					basename($this->file),
					$this->file,
					filesize($this->file),
					UPLOAD_ERR_OK
				);
			}
		);
	}
}

(new IdentifiedFiles())->run();