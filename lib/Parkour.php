<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */
namespace Parkour;

use Parkour\Operation;
use Closure;



/**
 *	A collection of utilities to manipulate arrays.
 */
class Parkour {

	/**
	 *	Filters each of the given values through a function.
	 *
	 *	@param array $data Data.
	 *	@param Closure $map Closure.
	 *	@return array Mapped data.
	 */
	public static function map(array $data, Closure $map) {
		$mapped = [];

		foreach ($data as $key => $value) {
			$mapped[$key] = $map($value, $key);
		}

		return $mapped;
	}



	/**
	 *	Boils down a list of values to a single value.
	 *
	 *	@param array $data Data.
	 *	@param Closure $reduce Closure.
	 *	@param mixed $memo Initial value.
	 *	@return mixed Result.
	 */
	public static function reduce(array $data, Closure $reduce, $memo = null) {
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
	 *	@param Closure $map Function to map values.
	 *	@param Closure $reduce Function to reduce values.
	 *	@param mixed $memo Initial value to reduce.
	 *	@return mixed Result.
	 */
	public function mapReduce(
		array $data,
		Closure $map,
		Closure $reduce,
		$memo = null
	) {
		return self::reduce(
			self::map($data, $map),
			$reduce,
			$memo
		);
	}



	/**
	 *	Executes a function on each of the given values and returns a
	 *	cunjunction of the results.
	 *
	 *	@see mapReduce()
	 *	@param array $data Data.
	 *	@param Closure $map Closure.
	 *	@param boolean $memo Initial result.
	 *	@return boolean Result.
	 */

	public static function reduceAnd(array $data, Closure $map, $memo = true) {
		return self::mapReduce($data, $map, Operation::cunjunct(), $memo);
	}



	/**
	 *	Executes a function on each of the given values and returns a
	 *	disjunction of the results.
	 *
	 *	@see mapReduce()
	 *	@param array $data Data.
	 *	@param Closure $map Closure.
	 *	@param boolean $memo Initial result.
	 *	@return boolean Result.
	 */
	public static function reduceOr(array $data, Closure $map, $memo = false) {
		return self::mapReduce($data, $map, Operation::disjunct(), $memo);
	}



	/**
	 *	Returns all values that pass a truth test.
	 *
	 *	@param array $data Data.
	 *	@param Closure $filter Filter function.
	 *	@return array Filtered data.
	 */
	public static function filter(array $data, Closure $filter, $preserveKeys = true) {
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
	 *	@param array $data Data.
	 *	@param Closure $index Index function.
	 *	@param boolean $overwrite Should duplicate keys be overwritten ?
	 *	@return array Indexed data.
	 */
	public static function combine(array $data, Closure $index, $overwrite = true) {
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
	 *	@param array $data Data.
	 *	@param Closure $closure Closure.
	 */
	public static function invoke(array $data, Closure $closure) {
		foreach ($data as $key => $value) {
			$closure($value, $key);
		}
	}
}
