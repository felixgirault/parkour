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
	 *	@param callable $callable Function to execute.
	 *	@param boolean $greedy When false, the itaration will stop on the
	 *		first falsy result.
	 *	@return boolean Result.
	 */
	public static function allOk(array $data, callable $callable, $greedy = false) {
		if ($greedy) {
			return self::mapReduce($data, $callable, new Conjunct(), true);
		}

		foreach ($data as $key => $value) {
			if (!$callable($value, $key)) {
				return false;
			}
		}

		return true;
	}



	/**
	 *	Executes a function on each of the given values and returns if at
	 *	least one result is truthy.
	 *
	 *	@param array $data Values.
	 *	@param callable $callable Function to execute.
	 *	@param boolean $greedy When false, the itaration will stop on the
	 *		first truthy result.
	 *	@return boolean Result.
	 */
	public static function oneOk(array $data, callable $callable, $greedy = false) {
		if ($greedy) {
			return self::mapReduce($data, $callable, new Disjunct(), false);
		}

		foreach ($data as $key => $value) {
			if ($callable($value, $key)) {
				return true;
			}
		}

		return false;
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

