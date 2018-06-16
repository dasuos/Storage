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
final class IdentifiedFiles extends Tester\TestCase {

	private $file;

	public function setup() {
		parent::setup();
		Tester\Environment::lock('IdentifiedFiles', __DIR__ . '/../Temp/Locks');
		$this->file = (new Storage\Misc\PngImage(
			__DIR__ . '/../Temp/IdentifiedFiles',
			800,
			600
		))->path();
	}

	public function testSavingFileWithProhibitFormat() {
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

	public function testSavingFileWithValidFormat() {
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

(new IdentifiedFiles)->run();