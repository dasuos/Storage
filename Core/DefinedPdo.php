<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

final class DefinedPdo extends \PDO {

	const OPTIONS = [
		\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
		\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
		\PDO::ATTR_EMULATE_PREPARES => false,
	];

	public function __construct($dsn, $user, $password) {
		parent::__construct($dsn, $user, $password, self::OPTIONS);
	}
}