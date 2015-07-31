<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */
namespace Parkour\Functor;



/**
 *
 */
class Identity {

	/**
	 *	Returns its first parameter.
	 *
	 *	@param mixed $value.
	 *	@return mixed $value
	 */
	public function __invoke($value) {
		return $value;
	}
}
