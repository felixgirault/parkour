<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */
namespace Parkour;

use Parkour\Functor\Conjunct;
use Parkour\Functor\Disjunct;



/**
 *	A collection of utilities to manipulate arrays.
 */
class Parkour {

	/**
	 *	Filters each of the given values through a function.
	 *
	 *	@param array $data Values.
	 *	@param callable $map Function to map values.
	 *	@return array Mapped data.
	 */
	public static function map(array $data, callable $map) {
		$mapped = [];

		foreach ($data as $key => $value) {
			$mapped[$key] = $map($value, $key);
		}

		return $mapped;
	}



	/**
	 *	Boils down a list of values to a single value.
	 *
	 *	@param array $data Values.
	 *	@param callable $reduce Function to reduce values.
	 *	@param mixed $memo Initial value.
	 *	@return mixed Result.
	 */
	public static function reduce(array $data, callable $reduce, $memo = null) {
		foreach ($data as $key => $value) {
			$memo = $reduce($memo, $value, $key);
		}

		return $memo;
	}



	/**
	 *	Maps and reduces a list of values.
	 *
	 *	@see map()
	 *	@see reduce()
	 *	@param array $data Values.
	 *	@param callable $map Function to map values.
	 *	@param callable $reduce Function to reduce values.
	 *	@param mixed $memo Initial value to reduce.
	 *	@return mixed Result.
	 */
	public static function mapReduce(
		array $data,
		callable $map,
		callable $reduce,
		$memo = null
	) {
		foreach ($data as $key => $value) {
			$mapped = $map($value, $key);
			$memo = $reduce($memo, $mapped, $key);
		}
		
		return $memo;
	}



	/**
	 *	Executes a function on each of the given values and returns a
	 *	cunjunction of the results.
	 *
	 *	@see mapReduce()
	 *	@param array $data Values.
	 *	@param callable $map Function to reduce values.
	 *	@param boolean $memo Initial result.
	 *	@return boolean Result.
	 */
	public static function reduceAnd(array $data, callable $map, $memo = true) {
		return self::mapReduce($data, $map, new Conjunct(), $memo);
	}



	/**
	 *	Executes a function on each of the given values and returns a
	 *	disjunction of the results.
	 *
	 *	@see mapReduce()
	 *	@param array $data Values.
	 *	@param callable $map Function to reduce values.
	 *	@param boolean $memo Initial result.
	 *	@return boolean Result.
	 */
	public static function reduceOr(array $data, callable $map, $memo = false) {
		return self::mapReduce($data, $map, new Disjunct(), $memo);
	}



	/**
	 *	Returns all values that pass a truth test.
	 *
	 *	@param array $data Values.
	 *	@param callable $filter Function to filter values.
	 *	@return array Filtered values.
	 */
	public static function filter(array $data, callable $filter, $preserveKeys = true) {
		$filtered = [];

		foreach ($data as $key => $value) {
			if (!$filter($value, $key)) {
				continue;
			}

			if ($preserveKeys) {
				$filtered[$key] = $value;
			} else {
				$filtered[] = $value;
			}
		}

		return $filtered;
	}



	/**
	 *
	 *
	 *	@param array $data Values.
	 *	@param callable $index Function to index values.
	 *	@param boolean $overwrite Should duplicate keys be overwritten ?
	 *	@return array Indexed values.
	 */
	public static function combine(array $data, callable $index, $overwrite = true) {
		$indexed = [];

		foreach ($data as $key => $value) {
			foreach ($index($value, $key) as $k => $v) {
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
	 *	@param array $data Values.
	 *	@param callable $callable Function to invoke on values.
	 */
	public static function invoke(array $data, callable $callable) {
		foreach ($data as $key => $value) {
			$callable($value, $key);
		}
	}
}
