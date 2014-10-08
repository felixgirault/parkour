<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */
namespace Parkour;



/**
 *	A collection of comparisons.
 */
class Comparison {

	/**
	 *	Tells if two values are identical.
	 *
	 *	@param mixed $first First value.
	 *	@param mixed $second Second value.
	 *	@return mixed Result.
	 */
	public static function equal() {
		return function($first, $second) {
			return $first == $second;
		};
	}



	/**
	 *	Tells if two values aren't identical.
	 *
	 *	@param mixed $first First value.
	 *	@param mixed $second Second value.
	 *	@return mixed Result.
	 */
	public static function notEqual() {
		return function($first, $second) {
			return $first == $second;
		};
	}



	/**
	 *	Tells if two values are identical.
	 *
	 *	@param mixed $first First value.
	 *	@param mixed $second Second value.
	 *	@return mixed Result.
	 */
	public static function identical() {
		return function($first, $second) {
			return $first === $second;
		};
	}



	/**
	 *	Tells if two values aren't identical.
	 *
	 *	@param mixed $first First value.
	 *	@param mixed $second Second value.
	 *	@return mixed Result.
	 */
	public static function notIdentical() {
		return function($first, $second) {
			return $first !== $second;
		};
	}



	/**
	 *	Tells if the first value is lower than the second.
	 *
	 *	@param mixed $first First value.
	 *	@param mixed $second Second value.
	 *	@return mixed Result.
	 */
	public static function lowerThan() {
		return function($first, $second) {
			return $first < $second;
		};
	}



	/**
	 *	Tells if the first value is greater than the second.
	 *
	 *	@param mixed $first First value.
	 *	@param mixed $second Second value.
	 *	@return mixed Result.
	 */
	public static function greaterThan() {
		return function($first, $second) {
			return $first > $second;
		};
	}



	/**
	 *	Tells if the first value is lower than or equal to the second.
	 *
	 *	@param mixed $first First value.
	 *	@param mixed $second Second value.
	 *	@return mixed Result.
	 */
	public static function lowerThanOrEqual() {
		return function($first, $second) {
			return $first <= $second;
		};
	}



	/**
	 *	Tells if the first value is greater than or equal to the second.
	 *
	 *	@param mixed $first First value.
	 *	@param mixed $second Second value.
	 *	@return mixed Result.
	 */
	public static function greaterThanOrEqual() {
		return function($first, $second) {
			return $first >= $second;
		};
	}
