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
	 *	@param int $calls Number of expected calls.
	 *	@return Closure Closure.
	 */
	public function closure(array $values, $calls = null) {
		if ($calls === null) {
			$calls = count($values);
		}

		$Mock = $this->getMock('stdClass', ['method']);

		$Mocker = $Mock->expects($this->exactly($calls));
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
	public function testFilterMap() {
		$data = [1, 2, 3];

		$mapper = $this->closure([
			[1, 0, 2],
			[3, 2, 6]
		]);
		
		$tester = $this->closure([
			[1, 0, true],
			[2, 1, false],
			[3, 2, true]
		]);
		
		$expected = [0 => 2, 2 => 6];
		
		$this->assertEquals(
			$expected,
			Parkour::filterMap($data, $tester, $mapper)
		);
	}


	/**
	 *
	 */
	public function testEvery() {
		$data = [1, 2];

		$closure = $this->closure([
			[1, 0, false],
			[2, 1, true]
		]);

		$this->assertFalse(Parkour::every($data, $closure));

		$closure = $this->closure([
			[1, 0, true],
			[2, 1, true]
		]);

		$this->assertTrue(Parkour::every($data, $closure));
	}



	/**
	 *
	 */
	public function testSome() {
		$data = [1, 2];

		$closure = $this->closure([
			[1, 0, false],
			[2, 1, false]
		]);

		$this->assertFalse(Parkour::some($data, $closure));

		$closure = $this->closure([
			[1, 0, true],
			[2, 1, false]
		]);

		$this->assertTrue(Parkour::some($data, $closure));
	}



	/**
	 *
	 */
	public function testFirstOk() {
		$data = [1, 2];

		$closure = $this->closure([
			[1, 0, false],
			[2, 1, false]
		]);

		$this->assertFalse(Parkour::firstOk($data, $closure));

		$closure = $this->closure([
			[1, 0, true],
			[2, 1, false]
		], 1);

		$this->assertEquals(0, Parkour::firstOk($data, $closure));
	}



	/**
	 *
	 */
	public function testFirstNotOk() {
		$data = [1, 2];

		$closure = $this->closure([
			[1, 0, true],
			[2, 1, true]
		]);

		$this->assertFalse(Parkour::firstNotOk($data, $closure));

		$closure = $this->closure([
			[1, 0, false],
			[2, 1, true]
		], 1);

		$this->assertEquals(0, Parkour::firstNotOk($data, $closure));
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

		$this->assertEquals(
			$expected,
			Parkour::filter($data, $closure)
		);
	}



	/**
	 *
	 */
	public function testPassing() {
		$data = [
			'a' => 1,
			'b' => 2
		];

		$closure = $this->closure([
			[1, 'a', false],
			[2, 'b', true]
		]);

		$expected = [2];

		$this->assertEquals(
			$expected,
			Parkour::passing($data, $closure, false)
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
	public function testEach() {
		$data = [
			'a' => 1,
			'b' => 2
		];

		$closure = $this->closure([
			[1, 'a', null],
			[2, 'b', null]
		]);

		Parkour::each($data, $closure);
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



	/**
	 *
	 */
	public function testRange() {
		$result = [];

		foreach (Parkour::range(0, 5) as $i) {
			$result[] = $i;
		}

		$this->assertEquals([0, 1, 2, 3, 4], $result);

		$result = [];

		foreach (Parkour::range(2, 7, 2) as $i) {
			$result[] = $i;
		}

		$this->assertEquals([2, 4, 6], $result);

		$result = [];

		foreach (Parkour::range(10, 2) as $i) {
			$result[] = $i;
		}

		$this->assertEquals([], $result);

		$result = [];

		foreach (Parkour::range(10, 5, -2) as $i) {
			$result[] = $i;
		}

		$this->assertEquals([10, 8, 6], $result);

		$result = [];

		foreach (Parkour::range(-4, -2) as $i) {
			$result[] = $i;
		}

		$this->assertEquals([-4, -3], $result);
	}
}
