<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Dasuos\Tests\Integration;

use Dasuos\Tests\TestCase\Database;
use Tester\Assert;
use Tester\TestCase;
use Dasuos\Storage;

require __DIR__ . '/../bootstrap.php';

final class SafeQuery extends TestCase {

	use Database;

	public function testFetchingAllRows() {
		$this->database->exec(
			"INSERT INTO test_table (id, test_value) VALUES (1, 'foo')"
		);
		$this->database->exec(
			"INSERT INTO test_table (id, test_value) VALUES (2, 'bar')"
		);
		Assert::equal(
			[
				['id' => 1, 'test_value' => 'foo',],
				['id' => 2, 'test_value' => 'bar',],
			],
			(new Storage\SafeQuery(
				$this->database
			))->rows('SELECT * FROM test_table')
		);
	}

	public function testThrowingCustomExceptionOnUniqueConstraint() {
		$this->database->exec(
			"INSERT INTO test_table (id, test_value) VALUES (1, 'foo')"
		);
		$exception = Assert::exception(function() {
				(new Storage\SafeQuery(
					$this->database
				))->rows("INSERT INTO test_table VALUES (2, 'foo')");
			},
			Storage\UniqueConstraintException::class,
			'Duplicate column value violates unique constraint',
			23505
		);
		Assert::type(\PDOException::class, $exception->getPrevious());
	}

	public function testFetchingSingleRow() {
		$this->database->exec(
			"INSERT INTO test_table (id, test_value) VALUES (1, 'foo')"
		);
		$this->database->exec(
			"INSERT INTO test_table (id, test_value) VALUES (2, 'bar')"
		);
		Assert::equal(
			['id' => 1, 'test_value' => 'foo',],
			(new Storage\SafeQuery(
				$this->database
			))->row('SELECT * FROM test_table')
		);
	}

	public function testFetchingWithPositionPlaceholder() {
		$this->database->exec(
			"INSERT INTO test_table (id, test_value) VALUES (1, 'foo')"
		);
		$this->database->exec(
			"INSERT INTO test_table (id, test_value) VALUES (2, 'bar')"
		);
		Assert::equal(
			[
				['id' => 1, 'test_value' => 'foo',],
			],
			(new Storage\SafeQuery(
				$this->database
			))->rows('SELECT * FROM test_table WHERE id = ?', [1])
		);
	}

	public function testFetchingWithNamedPlaceholder() {
		$this->database->exec(
			"INSERT INTO test_table (id, test_value) VALUES (1, 'foo')"
		);
		$this->database->exec(
			"INSERT INTO test_table (id, test_value) VALUES (2, 'bar')"
		);
		Assert::equal(
			[
				['id' => 1, 'test_value' => 'foo',],
			],
			(new Storage\SafeQuery(
				$this->database
			))->rows('SELECT * FROM test_table WHERE id = :id', ['id' => 1])
		);
	}

}

(new SafeQuery())->run();