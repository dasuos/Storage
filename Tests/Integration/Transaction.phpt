<?php
declare(strict_types = 1);

namespace Dasuos\Storage\Integration;

use Dasuos\Storage;
use Dasuos\Storage\TestCase\Database;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 * @phpVersion > 7.1
 */

final class Transaction extends TestCase {

	use Database;

	public function testCommittingQueries() {
		Assert::equal(
			[
				['id' => 1, 'test_value' => 'foo'],
				['id' => 2, 'test_value' => 'bar'],
			],
			(new Storage\Transaction($this->database))
				->begin(
					function() {
						$this->database->exec(
							"INSERT INTO test_table (id, test_value) 
							VALUES (1, 'foo')"
						);
						$this->database->exec(
							"INSERT INTO test_table (id, test_value) 
							VALUES (2, 'bar')"
						);
						return $this->database->query(
							'SELECT * FROM test_table'
						)->fetchAll(\PDO::FETCH_ASSOC);
					}
				)
		);
	}

	public function testRollingBackQueries() {
		Assert::exception(
			function() {
				(new Storage\Transaction($this->database))
					->begin(
						function() {
							$this->database->exec(
								"INSERT INTO test_table (id, test_value) 
								VALUES (1, 'foo')"
							);
							$this->database->exec(
								"INSERT INTO test_table (id, test_value) 
								VALUES (2, 'bar')"
							);
							throw new \UnexpectedValueException;
						}
					);
			},
			\Throwable::class
		);
		Assert::equal(
			[],
			$this->database->query(
				'SELECT * FROM test_table'
			)->fetchAll(\PDO::FETCH_ASSOC)
		);
	}
}

(new Transaction)->run();