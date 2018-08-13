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
final class FakeQuery extends TestCase {

	use Database;

	public function testFetchingFakeRow() {
		$this->database->exec(
			"INSERT INTO test_table (id, test_value) VALUES (1, 'foo')"
		);
		$this->database->exec(
			"INSERT INTO test_table (id, test_value) VALUES (2, 'bar')"
		);
		Assert::same(
			['foo', 'bar'],
			(new Storage\FakeQuery(
				new Storage\SafeQuery($this->database),
				['foo', 'bar']
			))->row('SELECT * FROM test_table WHERE id = 1')
		);
	}

	public function testFetchingFakeRowWithPlaceholder() {
		$this->database->exec(
			"INSERT INTO test_table (id, test_value) VALUES (1, 'foo')"
		);
		$this->database->exec(
			"INSERT INTO test_table (id, test_value) VALUES (2, 'bar')"
		);
		Assert::same(
			['foo', 'bar'],
			(new Storage\FakeQuery(
				new Storage\SafeQuery($this->database),
				['foo', 'bar']
			))->row('SELECT * FROM test_table WHERE id = ?', [1])
		);
	}

	public function testFetchingFakeRows() {
		$this->database->exec(
			"INSERT INTO test_table (id, test_value) VALUES (1, 'foo')"
		);
		$this->database->exec(
			"INSERT INTO test_table (id, test_value) VALUES (2, 'bar')"
		);
		Assert::same(
			['foo', 'bar'],
			(new Storage\FakeQuery(
				new Storage\SafeQuery($this->database),
				['foo', 'bar']
			))->rows('SELECT * FROM test_table')
		);
	}

	public function testFetchingFakeRowsWithPlaceholder() {
		$this->database->exec(
			"INSERT INTO test_table (id, test_value) VALUES (1, 'foo')"
		);
		$this->database->exec(
			"INSERT INTO test_table (id, test_value) VALUES (2, 'bar')"
		);
		Assert::same(
			['foo', 'bar'],
			(new Storage\FakeQuery(
				new Storage\SafeQuery($this->database),
				['foo', 'bar']
			))->rows('SELECT * FROM test_table WHERE test_value = ?', ['foo'])
		);
	}

	public function testFetchingFakeColumn() {
		$this->database->exec(
			"INSERT INTO test_table (id, test_value) VALUES (1, 'foo')"
		);
		Assert::same(
			['foo', 'bar'],
			(new Storage\FakeQuery(
				new Storage\SafeQuery($this->database),
				['foo', 'bar']
			))->column('SELECT test_value FROM test_table WHERE id = 1')
		);
	}

	public function testFetchingFakeColumnWithPlaceholder() {
		$this->database->exec(
			"INSERT INTO test_table (id, test_value) VALUES (1, 'foo')"
		);
		Assert::same(
			['foo', 'bar'],
			(new Storage\FakeQuery(
				new Storage\SafeQuery($this->database),
				['foo', 'bar']
			))->column('SELECT test_value FROM test_table WHERE id = ?', [1])
		);
	}

	public function testInsertingRow() {
		(new Storage\FakeQuery(
			new Storage\SafeQuery($this->database),
			['foo', 'bar']
		))->perform(
			"INSERT INTO test_table (id, test_value) VALUES (3, 'foobar')"
		);
		Assert::same(
			[['id' => 3, 'test_value' => 'foobar']],
			$this->database->query(
				'SELECT id, test_value FROM test_table'
			)->fetchAll()
		);
	}

	public function testUpdatingRow() {
		$this->database->exec(
			"INSERT INTO test_table (id, test_value) VALUES (1, 'foo')"
		);
		(new Storage\FakeQuery(
			new Storage\SafeQuery($this->database),
			['foo', 'bar']
		))->perform(
			"UPDATE test_table SET test_value = 'bar' WHERE id = 1"
		);
		Assert::same(
			[['id' => 1, 'test_value' => 'bar']],
			$this->database->query(
				'SELECT id, test_value FROM test_table'
			)->fetchAll()
		);
	}
}

(new FakeQuery())->run();