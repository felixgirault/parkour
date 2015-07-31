<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */
namespace Parkour;

use InvalidArgumentException;



/**
 *	A collection of utilities to manipulate arrays.
 */
class Parkour {

	/**
	 *	Iterates over the given data.
	 *
	 *	@param array $data Data.
	 *	@param callable $cb Function that receives values.
	 */
	public static function each(array $data, callable $cb) {
		array_walk($data, $cb);
	}

	/**
	 *	Updates each of the given values.
	 *
	 *	@param array $data Data.
	 *	@param callable $cb Function to map values.
	 *	@return array Mapped data.
	 */
	public static function map(array $data, callable $cb) {
		foreach ($data as $key => $value) {
			$data[$key] = call_user_func($cb, $value, $key);
		}

		return $data;
	}

	/**
	 *	Updates the keys each of the given data.
	 *
	 *	@param array $data Data.
	 *	@param callable $cb Function to map keys.
	 *	@return array Mapped data.
	 */
	public static function mapKeys(array $data, callable $cb) {
		$mapped = [];

		foreach ($data as $key => $value) {
			$mappedKey = call_user_func($cb, $value, $key);
			$mapped[$mappedKey] = $value;
		}

		return $mapped;
	}

	/**
	 *	Filters each of the given values through a function.
	 *
	 *	@param array $data Data.
	 *	@param callable $cb Function to filter values.
	 *	@return array Filtered data.
	 */
	public static function filter(array $data, callable $cb, $keyed = true) {
		return defined('ARRAY_FILTER_USE_BOTH')
			? self::nativeFilter($data, $cb, $keyed)
			: self::ownFilter($data, $cb, $keyed);
	}

	/**
	 *	Native implementation of filter().
	 *
	 *	@see filter()
	 */
	public static function nativeFilter(array $data, callable $cb, $keyed = true) {
		$filtered = array_filter($data, $cb, ARRAY_FILTER_USE_BOTH);

		if ($keyed) {
			return $filtered;
		}

		return array_values($filtered);
	}

	/**
	 *	Parkour's implementation of filter().
	 *
	 *	@see filter()
	 */
	public static function ownFilter(array $data, callable $cb, $keyed = true) {
		$filtered = [];

		foreach ($data as $key => $value) {
			if (call_user_func($cb, $value, $key)) {
				if ($keyed) {
					$filtered[$key] = $value;
				} else {
					$filtered[] = $value;
				}
			}
		}

		return $filtered;
	}

	/**
	 *	The opposite of filter().
	 *
	 *	@param array $data Data.
	 *	@param callable $cb Function to filter values.
	 *	@return array Filtered data.
	 */
	public static function reject(array $data, callable $cb, $keyed = true) {
		return self::filter($data, function($value, $key) use ($cb) {
			return !call_user_func($cb, $value, $key);
		}, $keyed);
	}

	/**
	 *	Boils down a list of values to a single value.
	 *
	 *	@param array $data Data.
	 *	@param callable $cb Function to reduce values.
	 *	@param mixed $memo Initial value.
	 *	@return mixed Result.
	 */
	public static function reduce(array $data, callable $cb, $memo = null) {
		foreach ($data as $key => $value) {
			$memo = call_user_func($cb, $memo, $value, $key);
		}

		return $memo;
	}

	/**
	 *	Finds a value in the given data.
	 *
	 *	@param array $data Data.
	 *	@param callable $cb Function to find value.
	 *	@param mixed $default Default value.
	 *	@return mixed Value.
	 */
	public static function find(array $data, callable $cb, $default = null) {
		$key = self::findKey($data, $cb);

		return ($key === null)
			? $default
			: $data[$key];
	}

	/**
	 *	Finds a value in the given data and returns its key.
	 *
	 *	@param array $data Data.
	 *	@param callable $cb Function to find value.
	 *	@param mixed $default Default value.
	 *	@return int|string Key.
	 */
	public static function findKey(array $data, callable $cb, $default = null) {
		foreach ($data as $key => $value) {
			if (call_user_func($cb, $value, $key)) {
				return $key;
			}
		}

		return $default;
	}

	/**
	 *	Returns true if some elements of the given data passes
	 *	a thruth test.
	 *
	 *	@param array $data Data.
	 *	@param callable $cb Test.
	 *	@return boolean If some elements passes the test.
	 */
	public static function some(array $data, callable $cb) {
		foreach ($data as $key => $value) {
			if (call_user_func($cb, $value, $key)) {
				return true;
			}
		}

		return false;
	}

	/**
	 *	Returns true if every element of the given data passes
	 *	a thruth test.
	 *
	 *	@param array $data Data.
	 *	@param callable $cb Test.
	 *	@return boolean If every element passes the test.
	 */
	public static function every(array $data, callable $cb) {
		foreach ($data as $key => $value) {
			if (!call_user_func($cb, $value, $key)) {
				return false;
			}
		}

		return true;
	}

	/**
	 *	Indexes an array depending on the values it contains.
	 *
	 *	@param array $data Data.
	 *	@param callable $cb Function to combine values.
	 *	@param boolean $overwrite Should duplicate keys be overwritten ?
	 *	@return array Indexed values.
	 */
	public static function combine(array $data, callable $cb, $overwrite = true) {
		$combined = [];

		foreach ($data as $key => $value) {
			$Combinator = call_user_func($cb, $value, $key);
			$index = $Combinator->key();

			if ($overwrite || !isset($combined[$index])) {
				$combined[$index] = $Combinator->current();
			}
		}

		return $combined;
	}

	/**
	 *	Makes every value that is numerically indexed a key, given $default
	 *	as value.
	 *
	 *	@param array $data Data.
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
	 *	Reindexes a list of values.
	 *
	 *	@param array $data Data.
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
	 *	Merges two arrays recursively.
	 *
	 *	@param array $first Original data.
	 *	@param array $second Data to be merged.
	 *	@return array Merged data.
	 */
	public static function merge(array $first, array $second) {
		foreach ($second as $key => $value) {
			$shouldBeMerged = (
				isset($first[$key])
				&& is_array($first[$key])
				&& is_array($value)
			);

			$first[$key] = $shouldBeMerged
				? self::merge($first[$key], $value)
				: $value;
		}

		return $first;
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

	/**
	 *	Tells if there is data at the given path.
	 *
	 *	@see splitPath()
	 *	@param array $data Data.
	 *	@param array|string $path Path.
	 *	@return boolean If there is data.
	 */
	public static function has(array $data, $path) {
		$keys = self::splitPath($path);
		$current = $data;

		foreach ($keys as $key) {
			if (!isset($current[$key])) {
				return false;
			}

			$current = $current[$key];
		}

		return true;
	}

	/**
	 *	Returns the value at the given path.
	 *
	 *	@see splitPath()
	 *	@param array $data Data.
	 *	@param array|string $path Path.
	 *	@param mixed $default Default value.
	 *	@return mixed Value.
	 */
	public static function get(array $data, $path, $default = null) {
		$keys = self::splitPath($path);
		$current = $data;

		foreach ($keys as $key) {
			if (!isset($current[$key])) {
				return $default;
			}

			$current = $current[$key];
		}

		return $current;
	}

	/**
	 *	Sets data at the given path.
	 *
	 *	@see splitPath()
	 *	@param array $data Data.
	 *	@param array|string $path Path.
	 *	@param mixed $value Value.
	 *	@return mixed Updated data.
	 */
	public static function set(array $data, $path, $value) {
		$keys = self::splitPath($path);
		$current =& $data;

		foreach ($keys as $key) {
			if (!is_array($current)) {
				return $data;
			}

			if (!isset($current[$key])) {
				$current[$key] = [];
			}

			$current =& $current[$key];
		}

		$current = $value;
		return $data;
	}

	/**
	 *	Updates data at the given path.
	 *
	 *	@see splitPath()
	 *	@param array $data Data.
	 *	@param array|string $path Path.
	 *	@param callable $cb Callback to update the value.
	 *	@return mixed Updated data.
	 */
	public static function update(array $data, $path, callable $cb) {
		$keys = self::splitPath($path);
		$current =& $data;

		foreach ($keys as $key) {
			if (!isset($current[$key])) {
				return $data;
			}

			$current =& $current[$key];
		}

		$current = call_user_func($cb, $current);
		return $data;
	}

	/**
	 *	Splits a path into multiple keys.
	 *
	 *	@param array|string $path Path.
	 *	@return array Keys.
	 */
	public static function splitPath($path) {
		$keys = is_string($path)
			? array_filter(explode('.', $path))
			: $path;

		if (!is_array($keys)) {
			throw new InvalidArgumentException(
				'The path should be either an array or a string.'
			);
		}

		if (empty($keys)) {
			throw new InvalidArgumentException(
				'The path should not be empty.'
			);
		}

		return $keys;
	}
}
