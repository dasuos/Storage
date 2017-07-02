<?php
declare(strict_types = 1);
namespace Dasuos\Tests\TestCase;

use Dasuos\Storage;
use Tester\Environment;

trait Database {
	/**
	 * @var \PDO
	 */
	protected $database;

	public function setUp() {
		Environment::lock('database', __DIR__ . '/../temp');
		$credentials = parse_ini_file(
			__DIR__ . '/../TestCase/database.local.ini'
		);
		$this->database = new Storage\DefinedPdo(
			$credentials['dsn'],
			$credentials['user'],
			$credentials['password']
		);
		$this->database->exec(
			"SELECT truncate_tables('postgres'); 
			SELECT restart_sequences();"
		);
	}
}