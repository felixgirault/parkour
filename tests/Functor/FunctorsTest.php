<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */
namespace Parkour\Functor;

use PHPUnit_Framework_TestCase as TestCase;



/**
 *
 */
class FunctorsTest extends TestCase {

	/**
	 *
	 */
	public function testAdd() {
		$Add = new Add();
		$this->assertEquals(4, $Add(2, 2));
	}



	/**
	 *
	 */
	public function testSubstract() {
		$Substract = new Substract();
		$this->assertEquals(2, $Substract(4, 2));
	}



	/**
	 *
	 */
	public function testMultiply() {
		$Multiply = new Multiply();
		$this->assertEquals(4, $Multiply(2, 2));
	}



	/**
	 *
	 */
	public function testDivide() {
		$Divide = new Divide();
		$this->assertEquals(2, $Divide(4, 2));
	}



	/**
	 *
	 */
	public function testConjunct() {
		$Conjunct = new Conjunct();
		$this->assertFalse($Conjunct(true, false));
	}



	/**
	 *
	 */
	public function testDisjunct() {
		$Disjunct = new Disjunct();
		$this->assertTrue($Disjunct(true, false));
	}



	/**
	 *
	 */
	public function testEqual() {
		$Equal = new Equal();
		$this->assertTrue($Equal('2', 2));
	}



	/**
	 *
	 */
	public function testNotEqual() {
		$NotEqual = new NotEqual();
		$this->assertTrue($NotEqual(4, 2));
	}



	/**
	 *
	 */
	public function testIdentical() {
		$Identical = new Identical();
		$this->assertTrue($Identical(2, 2));
	}



	/**
	 *
	 */
	public function testNotIdentical() {
		$NotIdentical = new NotIdentical();
		$this->assertTrue($NotIdentical('2', 2));
	}



	/**
	 *
	 */
	public function testLower() {
		$Lower = new Lower();
		$this->assertTrue($Lower(2, 4));
	}



	/**
	 *
	 */
	public function testGreater() {
		$Greater = new Greater();
		$this->assertTrue($Greater(4, 2));
	}



	/**
	 *
	 */
	public function testLowerOrEqual() {
		$LowerOrEqual = new LowerOrEqual();
		$this->assertTrue($LowerOrEqual(2, 2));
		$this->assertTrue($LowerOrEqual(2, 4));
	}



	/**
	 *
	 */
	public function testGreaterOrEqual() {
		$GreaterOrEqual = new GreaterOrEqual();
		$this->assertTrue($GreaterOrEqual(2, 2));
		$this->assertTrue($GreaterOrEqual(4, 2));
	}
}
