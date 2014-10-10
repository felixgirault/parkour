<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */
namespace Parkour;

use PHPUnit_Framework_TestCase as TestCase;
use ReflectionClass;



/**
 *
 */
class ParkourTest extends TestCase {

	/**
	 *
	 */
	public function testMap() {
		$data = [
			'a' => 1,
			'b' => 2
		];

		$closure = Utility::closure($this, [
			[1, 'a', 2],
			[2, 'b', 4]
		]);

		$expected = [
			'a' => 2,
			'b' => 4
		];

		$this->assertEquals(
			$expected,
			Parkour::map($data, $closure)
		);
	}



	/**
	 *
	 */
	public function testReduce() {
		$data = [1, 2];

		$closure = Utility::closure($this, [
			[0, 1, 0, 1],
			[1, 2, 1, 3]
		]);

		$expected = 3;

		$this->assertEquals(
			$expected,
			Parkour::reduce($data, $closure, 0)
		);
	}



	/**
	 *
	 */
	public function testMapReduce() {
		$data = [1, 2];

		$mapper = Utility::closure($this, [
			[1, 0, 2],
			[2, 1, 4]
		]);

		$reducer = Utility::closure($this, [
			[2, 2, 0, 4],
			[4, 4, 1, 8]
		]);

		$expected = 8;

		$this->assertEquals(
			$expected,
			Parkour::mapReduce($data, $mapper, $reducer, 2)
		);
	}



	/**
	 *
	 */
	public function testAllOk() {
		$data = [1, 2];

		$closure = Utility::closure($this, [
			[1, 0, true],
			[2, 1, false]
		]);

		$this->assertFalse(Parkour::allOk($data, $closure));

		$closure = Utility::closure($this, [
			[1, 0, true],
			[2, 1, true]
		]);

		$this->assertTrue(Parkour::allOk($data, $closure));
	}



	/**
	 *
	 */
	public function testOneOk() {
		$data = [1, 2];

		$closure = Utility::closure($this, [
			[1, 0, false],
			[2, 1, false]
		]);

		$this->assertFalse(Parkour::oneOk($data, $closure));

		$closure = Utility::closure($this, [
			[1, 0, false],
			[2, 1, true]
		]);

		$this->assertTrue(Parkour::oneOk($data, $closure));
	}



	/**
	 *
	 */
	public function testFilter() {
		$data = [
			'a' => 1,
			'b' => 2
		];

		$closure = Utility::closure($this, [
			[1, 'a', false],
			[2, 'b', true]
		]);

		$expected = [
			'b' => 2
		];

		// preserving keys
		$this->assertEquals(
			$expected,
			Parkour::filter($data, $closure)
		);

		$expected = [2];

		// not preserving keys
		$this->assertEquals(
			$expected,
			Parkour::filter($data, $closure, false)
		);
	}



	/**
	 *
	 */
	public function testCombine() {
		$users = [
			['id' => 1, 'name' => 'a'],
			['id' => 2, 'name' => 'b'],
			['id' => 3, 'name' => 'b']
		];

		$closure = function($user) {
			yield $user['name'] => $user['id'];
		};

		$expected = [
			'a' => 1,
			'b' => 3
		];

		// overwriting existing names
		$this->assertEquals(
			$expected,
			Parkour::combine($users, $closure)
		);

		$expected = [
			'a' => 1,
			'b' => 2
		];

		// not overwriting existing names
		$this->assertEquals(
			$expected,
			Parkour::combine($users, $closure, false)
		);
	}



	/**
	 *
	 */
	public function testInvoke() {
		$data = [
			'a' => 1,
			'b' => 2
		];

		$closure = Utility::closure($this, [
			[1, 'a', null],
			[2, 'b', null]
		]);

		Parkour::invoke($data, $closure);
	}
}



/**
 *	Builds testable closures.
 */
class Utility {

	/**
	 *	Name of the method to mock.
	 *
	 *	@var string
	 */
	const method = 'method';



	/**
	 *	Returns a closure constrained by the given values.
	 *
	 *	@see https://phpunit.de/manual/current/en/test-doubles.html#test-doubles.stubs.examples.StubTest5.php
	 *	@param TestCase $Test Test case using the factory.
	 *	@param array $values Values.
	 *	@return Closure Closure.
	 */
	public static function closure(TestCase $Test, array $values) {
		$Mock = $Test->getMock('stdClass', [
			self::method
		]);

		$Mock->expects($Test->any())
			->method(self::method)
			->will($Test->returnValueMap($values));

		$Reflection = new ReflectionClass($Mock);
		$Method = $Reflection->getMethod(self::method);

		return $Method->getClosure($Mock);
	}
}
