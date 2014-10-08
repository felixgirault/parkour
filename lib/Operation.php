<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */
namespace Parkour;



/**
 *	A collection of operations.
 */
class Operation {

	/**
	 *	Adds two values.
	 *
	 *	@param mixed $first First value.
	 *	@param mixed $second Second value.
	 *	@return mixed Result.
	 */
	public static function add() {
		return function($first, $second) {
			return $first + $second;
		};
	}



	/**
	 *	Substracts two values.
	 *
	 *	@param mixed $first First value.
	 *	@param mixed $second Second value.
	 *	@return mixed Result.
	 */
	public static function substract() {
		return function($first, $second) {
			return $first - $second;
		};
	}



	/**
	 *	Multiplies two values.
	 *
	 *	@param mixed $first First value.
	 *	@param mixed $second Second value.
	 *	@return mixed Result.
	 */
	public static function multiply() {
		return function($first, $second) {
			return $first * $second;
		};
	}



	/**
	 *	Divides two values.
	 *
	 *	@param mixed $first First value.
	 *	@param mixed $second Second value.
	 *	@return mixed Result.
	 */
	public static function divide() {
		return function($first, $second) {
			return $first / $second;
		};
	}



	/**
	 *	Calculates the conjunction of two values.
	 *
	 *	@param mixed $first First value.
	 *	@param mixed $second Second value.
	 *	@return mixed Result.
	 */
	public static function conjunct() {
		return function($first, $second) {
			return $first && $second;
		};
	}



	/**
	 *	Calculates the disjunction of two values.
	 *
	 *	@param mixed $first First value.
	 *	@param mixed $second Second value.
	 *	@return mixed Result.
	 */
	public static function disjunct() {
		return function($first, $second) {
			return $first || $second;
		};
	}
}
