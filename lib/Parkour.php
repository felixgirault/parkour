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
	 *	Executes a function on each of the given values and returns if all
	 *	results are truthy.
	 *
	 *	@param array $data Values.
	 *	@param callable $test Function to execute.
	 *	@return boolean Result.
	 */
	public static function allOk(array $data, callable $test) {
		return self::mapReduce($data, $test, new Conjunct(), true);
	}



	/**
	 *	Executes a function on each of the given values and returns if at
	 *	least one result is truthy.
	 *
	 *	@param array $data Values.
	 *	@param callable $test Function to execute.
	 *	@return boolean Result.
	 */
	public static function oneOk(array $data, callable $test) {
		return self::mapReduce($data, $test, new Disjunct(), false);
	}



	/**
	 *	Executes a function on each of the given values and returns the key
	 *	passed to the first passing function.
	 *
	 *	@param array $data Values.
	 *	@param callable $test Function to execute.
	 *	@return boolean Result.
	 */
	public static function firstOk(array $data, callable $test) {
		foreach ($data as $key => $value) {
			if ($test($value, $key)) {
				return $key;
			}
		}

		return false;
	}



	/**
	 *	Executes a function on each of the given values and returns the key
	 *	passed to the first failing function.
	 *
	 *	@param array $data Values.
	 *	@param callable $test Function to execute.
	 *	@return boolean Result.
	 */
	public static function firstNotOk(array $data, callable $test) {
		foreach ($data as $key => $value) {
			if (!$test($value, $key)) {
				return $key;
			}
		}

		return false;
	}



	/**
	 *	Returns all values that pass a truth test, keeping their keys.
	 *
	 *	@param array $data Values.
	 *	@param callable $test Function to test values.
	 *	@return array Filtered values.
	 */
	public static function filter(array $data, callable $test) {
		$filtered = [];

		foreach ($data as $key => $value) {
			if ($test($value, $key)) {
				$filtered[$key] = $value;
			}
		}

		return $filtered;
	}



	/**
	 *	Returns all values that pass a truth test.
	 *
	 *	@param array $data Values.
	 *	@param callable $test Function to test values.
	 *	@return array Filtered values.
	 */
	public static function passing(array $data, callable $test) {
		$passing = [];

		foreach ($data as $key => $value) {
			if ($test($value, $key)) {
				$passing[] = $value;
			}
		}

		return $passing;
	}



	/**
	 *	Indexes an array depending on the values it contains.
	 *
	 *	@param array $data Values.
	 *	@param callable $combine Function to combine values.
	 *	@param boolean $overwrite Should duplicate keys be overwritten ?
	 *	@return array Indexed values.
	 */
	public static function combine(array $data, callable $combine, $overwrite = true) {
		$combined = [];

		foreach ($data as $key => $value) {
			$Combinator = $combine($value, $key);
			$index = $Combinator->key();

			if ($overwrite || !isset($combined[$index])) {
				$combined[$index] = $Combinator->current();
			}
		}

		return $combined;
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



	/**
	 *	Reindexes a list of values.
	 *
	 *	@param array $data Values.
	 *	@param array $map An map of correspondances of the form
	 *		['currentIndex' => 'newIndex'].
	 *	@return boolean $keepUnmapped Whether or not to keep keys that are not
	 *		remapped.
	 *	@return array Reindexed values.
	 */
	public static function reindex(array $data, array $map, $keepUnmapped = true) {
		$reindexed = $keepUnmapped
			? $data
			: [];

		foreach ($map as $from => $to) {
			if (isset($data[$from])) {
				$reindexed[$to] = $data[$from];
			}
		}

		return $reindexed;
	}



	/**
	 *	Makes every value that is numerically indexed a key, given $default
	 *	as value.
	 *
	 *	@param array $data Values.
	 *	@param mixed $default Default value.
	 *	@return array Normalized values.
	 */
	public static function normalize(array $data, $default) {
		$normalized = [];

		foreach ($data as $key => $value) {
			if (is_numeric($key)) {
				$key = $value;
				$value = $default;
			}

			$normalized[$key] = $value;
		}

		return $normalized;
	}



	/**
	 *	Yields numbers in a given interval.
	 *	This method is a port of the range() function from underscore.js.
	 *
	 *	@see http://underscorejs.org/docs/underscore.html#section-60
	 *	@param int $from Start value.
	 *	@param int $to End value (not inclusive).
	 *	@param int $step Step.
	 *	@yields int Value.
	 */
	public static function range($from, $to, $step = null) {
		$step = $step ?: 1;
		$count = max(ceil(($to - $from) / $step), 0);

		for ($i = 0; $i < $count; $i++, $from += $step) {
			yield $from;
		}
	}
}

