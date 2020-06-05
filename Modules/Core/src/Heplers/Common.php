<?php

namespace Modules\Core\Helpers;

use BadMethodCallException;

class Common {


	public static function parseMethodName($method){
		preg_match_all('/((?:^|[A-Z])[a-z]+)/', $method, $result);
		$matches = @$result[0];

		if (!$matches) {
			throw new BadMethodCallException("Undefined method $method");
		}

		return collect($matches);
	}


	public static function startWith($name, $toMatch){
		$length = strlen($toMatch);
		return (substr($name, 0, $length) === $toMatch);
	}

	public static function getFunctionAttribute($name, $toMatch){
		$toMatchLength = strlen($toMatch);
		$fullLength = strlen($name);

		return substr($name, $toMatchLength, $fullLength);
	}

}