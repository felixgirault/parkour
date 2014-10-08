<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */
namespace Parkour;

use Closure;



/**
 *	A collection of utilities to manipulate arrays.
 */
class Parkour {

	/**
	 *	Filters each of the given values through a function.
	 *
	 *	@param array $data Data.
	 *	@param Closure $closure Closure.
	 *	@return array Mapped data.
	 */
	public static function map(array $data, Closure $closure) {
		$mapped = [];

		foreach ($data as $key => $value) {
			$mapped[$key] = $closure($value, $key);
		}

		return $mapped;
	}



	/**
	 *	Boils down a list of values to a single value.
	 *
	 *	@param array $data Data.
	 *	@param Closure $closure Closure.
	 *	@param mixed $memo Initial value.
	 *	@return mixed Result.
	 */
	public static function reduce(array $data, Closure $closure, $memo = null) {
		foreach ($data as $key => $value) {
			$memo = $closure($memo, $value, $key);
		}

		return $memo;
	}



	/**
	 *	Executes a function on each of the given values and returns a
	 *	cunjunction of the results.
	 *
	 *	@param array $data Data.
	 *	@param Closure $closure Closure.
	 *	@param boolean $result Initial result.
	 *	@return boolean Result.
	 */
	public static function reduceAnd(array $data, Closure $closure, $result = true) {
		return self::reduce($data, function($result, $value, $key) use ($closure) {
			return $result && $closure($value, $key);
		}, $result);
	}



	/**
	 *	Executes a function on each of the given values and returns a
	 *	disjunction of the results.
	 *
	 *	@param array $data Data.
	 *	@param Closure $closure Closure.
	 *	@param boolean $result Initial result.
	 *	@return boolean Result.
	 */
	public static function reduceOr(array $data, Closure $closure, $result = false) {
		return self::reduce($data, function($result, $value, $key) use ($closure) {
			return $result || $closure($value, $key);
		}, $result);
	}



	/**
	 *	Returns all values that pass a truth test.
	 *
	 *	@param array $data Data.
	 *	@param Closure $filter Filter function.
	 *	@return array Filtered data.
	 */
	public static function filter(array $data, Closure $filter) {
		$filtered = [];

		foreach ($data as $key => $value) {
			if ($filter($value, $key)) {
				$filtered[$key] = $value;
			}
		}

		return $filtered;
	}



	/**
	 *
	 *
	 *	@param array $data Data.
	 *	@param Closure $indexer Index function.
	 *	@param boolean $overwrite Should duplicate keys be overwritten ?
	 *	@return array Indexed data.
	 */
	public static function combine(array $data, Closure $indexer, $overwrite = true) {
		$indexed = [];

		foreach ($data as $key => $value) {
			foreach ($indexer($value, $key) as $k => $v) {
				if ($overwrite || !isset($indexed[$k])) {
					$indexed[$k] = $v;
				}
			}
		}

		return $indexed;
	}



	/**
	 *	Invokes a callback on each key/value pair of the given data.
	 *
	 *	@param array $data Data.
	 *	@param Closure $closure Closure.
	 */
	public static function invoke(array $data, Closure $closure) {
		foreach ($data as $key => $value) {
			$closure($value, $key);
		}
	}
}
