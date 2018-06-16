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
				['id' => 1, 'test_value' => 'foo'],
				['id' => 2, 'test_value' => 'bar'],
			],
			(new Storage\SafeQuery(
				$this->database
			))->rows('SELECT * FROM test_table')
		);
	}

	public function testFetchingNonexistentRows() {
		Assert::equal(
			[],
			(new Storage\SafeQuery(
				$this->database
			))->rows("SELECT * FROM test_table WHERE test_value = 'nonexistent'")
		);
	}

	public function testThrowingCustomExceptionOnUniqueConstraint() {
		$this->database->exec(
			"INSERT INTO test_table (id, test_value) VALUES (1, 'foo')"
		);
		$exception = Assert::exception(
			function() {
				(new Storage\SafeQuery(
					$this->database
				))->perform("INSERT INTO test_table VALUES (2, 'foo')");
			},
			\Dasuos\Storage\UniqueConstraintException::class,
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
			['id' => 1, 'test_value' => 'foo'],
			(new Storage\SafeQuery(
				$this->database
			))->row('SELECT * FROM test_table')
		);
	}

	public function testFetchingNonexistentRow() {
		Assert::equal(
			[],
			(new Storage\SafeQuery(
				$this->database
			))->row("SELECT * FROM test_table WHERE test_value = 'nonexistent'")
		);
	}

	public function testFetchingSingleValueInRow() {
		$this->database->exec(
			"INSERT INTO test_table (id, test_value) VALUES (1, 'foo')"
		);
		Assert::equal(
			['id' => 1],
			(new Storage\SafeQuery(
				$this->database
			))->row("SELECT id FROM test_table WHERE test_value = 'foo'")
		);
	}

	public function testFetchingSingleStringColumn() {
		$this->database->exec(
			"INSERT INTO test_table (id, test_value) VALUES (1, 'foo')"
		);
		Assert::equal(
			'foo',
			(new Storage\SafeQuery(
				$this->database
			))->column('SELECT test_value FROM test_table WHERE id = 1')
		);
	}

	public function testFetchingSingleIntegerColumn() {
		$this->database->exec(
			"INSERT INTO test_table (id, test_value) VALUES (1, 'foo')"
		);
		Assert::equal(
			1,
			(new Storage\SafeQuery(
				$this->database
			))->column("SELECT id FROM test_table WHERE test_value = 'foo'")
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
				['id' => 1, 'test_value' => 'foo'],
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
			[['id' => 1, 'test_value' => 'foo']],
			(new Storage\SafeQuery(
				$this->database
			))->rows('SELECT * FROM test_table WHERE id = :id', ['id' => 1])
		);
	}

	public function testPerformingQueryWithoutPlaceholders() {
		$query = new Storage\SafeQuery($this->database);
		$query->perform(
			"INSERT INTO test_table (id, test_value) VALUES (1, 'foo')"
		);
		Assert::equal(
			[
				['id' => 1, 'test_value' => 'foo'],
			],
			$query->rows('SELECT * FROM test_table WHERE id = 1')
		);
	}

	public function testPerformingQueryWithNamedPlaceholder() {
		$query = new Storage\SafeQuery($this->database);
		$query->perform(
			'INSERT INTO test_table (id, test_value) VALUES (1, :test_value)',
			['test_value' => 'foo']
		);
		Assert::equal(
			[
				['id' => 1, 'test_value' => 'foo'],
			],
			$query->rows('SELECT * FROM test_table WHERE id = 1')
		);
	}

	public function testPerformingQueryWithPositionPlaceholder() {
		$query = new Storage\SafeQuery($this->database);
		$query->perform(
			'INSERT INTO test_table (id, test_value) VALUES (1, ?)',
			['foo']
		);
		Assert::equal(
			[
				['id' => 1, 'test_value' => 'foo'],
			],
			$query->rows('SELECT * FROM test_table WHERE id = 1')
		);
	}
}

(new SafeQuery)->run();