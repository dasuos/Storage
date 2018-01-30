<?php
declare(strict_types = 1);
namespace Dasuos\Storage;

interface Transaction {
	/**
	 * @return mixed
	 */
	public function begin(\Closure $closure);
}