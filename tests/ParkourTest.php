<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */
namespace Parkour;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_Matcher_ConsecutiveParameters as ConsecutiveParameters;
use ReflectionClass;



/**
 *
 */
class ParkourTest extends TestCase {

	/**
	 *	Returns a closure constrained by the given values.
	 *
	 *	@see https://phpunit.de/manual/current/en/test-doubles.html#test-doubles.stubs.examples.StubTest5.php
	 *	@see https://phpunit.de/manual/current/en/test-doubles.html#test-doubles.mock-objects.examples.with-consecutive.php
	 *	@param array $values Values.
	 *	@return Closure Closure.
	 */
	public function closure(array $values) {
		$Mock = $this->getMock('stdClass', ['method']);

		$Mocker = $Mock->expects($this->exactly(count($values)));
		$Mocker->method('method');
		$Mocker->will($this->returnValueMap($values));

		$with = array_map(function($arguments) {
			return array_slice($arguments, 0, -1);
		}, $values);

		$Matcher = new ConsecutiveParameters($with);
		$Mocker->getMatcher()->parametersMatcher = $Matcher;

		$Reflection = new ReflectionClass($Mock);
		$Method = $Reflection->getMethod('method');

		return $Method->getClosure($Mock);
	}



	/**
	 *
	 */
	public function testMap() {
		$data = [
			'a' => 1,
			'b' => 2
		];

		$closure = $this->closure([
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

		$closure = $this->closure([
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

		$mapper = $this->closure([
			[1, 0, 2],
			[2, 1, 4]
		]);

		$reducer = $this->closure([
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

		$closure = $this->closure([
			[1, 0, true],
			[2, 1, false]
		]);

		$this->assertFalse(Parkour::allOk($data, $closure));

		$closure = $this->closure([
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

		$closure = $this->closure([
			[1, 0, false],
			[2, 1, false]
		]);

		$this->assertFalse(Parkour::oneOk($data, $closure));

		$closure = $this->closure([
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

		$closure = $this->closure([
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

		$closure = $this->closure([
			[1, 'a', false],
			[2, 'b', true]
		]);

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

		$closure = $this->closure([
			[1, 'a', null],
			[2, 'b', null]
		]);

		Parkour::invoke($data, $closure);
	}



	/**
	 *
	 */
	public function testReindex() {
		$data = ['foo' => 'bar'];
		$map = ['foo' => 'baz'];

		$expected = [
			'foo' => 'bar',
			'baz' => 'bar'
		];

		$this->assertEquals(
			$expected,
			Parkour::reindex($data, $map)
		);

		$expected = ['baz' => 'bar'];

		$this->assertEquals(
			$expected,
			Parkour::reindex($data, $map, false)
		);
	}



	/**
	 *
	 */
	public function testNormalize() {
		$data = [
			'one',
			'two' => 'three',
			'four'
		];

		$default = 'default';

		$expected = [
			'one' => $default,
			'two' => 'three',
			'four' => $default
		];

		$this->assertEquals(
			$expected,
			Parkour::normalize($data, $default)
		);
	}
}
