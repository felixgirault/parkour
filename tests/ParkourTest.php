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
	private function closure(array $values, $calls = null) {
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
	private function aggregate($generator) {
		$data = [];

		foreach ($generator as $value) {
			$data[] = $value;
		}

		return $data;
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
	public function testMapKeys() {
		$data = [
			'a' => 1,
			'b' => 2
		];

		$closure = $this->closure([
			[1, 'a', 'c'],
			[2, 'b', 'd']
		]);

		$expected = [
			'c' => 1,
			'd' => 2
		];

		$this->assertEquals(
			$expected,
			Parkour::mapKeys($data, $closure)
		);
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
	public function testOwnFilter() {
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
			Parkour::ownFilter($data, $closure)
		);
	}

	/**
	 *
	 */
	public function testReject() {
		$data = [
			'a' => 1,
			'b' => 2
		];

		$closure = $this->closure([
			[1, 'a', false],
			[2, 'b', true]
		]);

		$expected = [
			'a' => 1
		];

		$this->assertEquals(
			$expected,
			Parkour::reject($data, $closure)
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
	public function testSome() {
		$data = [1, 2];

		$closure = $this->closure([
			[1, 0, false],
			[2, 1, false]
		]);

		$this->assertFalse(Parkour::some($data, $closure));

		$closure = $this->closure([
			[1, 0, true]
		]);

		$this->assertTrue(Parkour::some($data, $closure));
	}

	/**
	 *
	 */
	public function testEvery() {
		$data = [1, 2];

		$closure = $this->closure([
			[1, 0, false]
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
	public function testMerge() {
		$first = [
			'one' => 1,
			'two' => 2,
			'three' => [
				'four' => 4,
				'five' => 5
			]
		];

		$second = [
			'two' => 'two',
			'three' => [
				'four' => 'four'
			]
		];

		$expected = [
			'one' => 1,
			'two' => 'two',
			'three' => [
				'four' => 'four',
				'five' => 5
			]
		];

		$this->assertEquals(
			$expected,
			Parkour::merge($first, $second)
		);
	}

	/**
	 *
	 */
	public function testRange() {
		$this->assertEquals(
			[0, 1, 2, 3, 4],
			$this->aggregate(Parkour::range(0, 5))
		);

		$this->assertEquals(
			[2, 4, 6],
			$this->aggregate(Parkour::range(2, 7, 2))
		);

		$this->assertEquals(
			[],
			$this->aggregate(Parkour::range(10, 2))
		);

		$this->assertEquals(
			[10, 8, 6],
			$this->aggregate(Parkour::range(10, 5, -2))
		);

		$this->assertEquals(
			[-4, -3],
			$this->aggregate(Parkour::range(-4, -2))
		);
	}

	/**
	 *
	 */
	public function testHas() {
		$data = [
			'a' => 1,
			'b' => [
				'c' => 2,
				'd' => [
					'e' => 3
				]
			]
		];

		$this->assertTrue(Parkour::has($data, 'a'));
		$this->assertTrue(Parkour::has($data, 'b.d.e'));
		$this->assertFalse(Parkour::has($data, 'a.b.c'));
	}

	/**
	 *
	 */
	public function testGet() {
		$data = [
			'a' => 1,
			'b' => [
				'c' => 2,
				'd' => [
					'e' => 3
				]
			]
		];

		$this->assertNull(Parkour::get($data, 'a.b.c'));
		$this->assertEquals(1, Parkour::get($data, 'a'));
		$this->assertEquals(3, Parkour::get($data, 'b.d.e'));
		$this->assertEquals('z', Parkour::get($data, 'a.b.c', 'z'));
	}

	/**
	 *
	 */
	public function testSet() {
		$data = [
			'a' => 1,
			'b' => [
				'c' => 2,
				'd' => [
					'e' => 3
				]
			]
		];

		$expected = [
			'a' => 'a',
			'b' => [
				'c' => 2,
				'd' => [
					'e' => 'e'
				],
				'f' => [
					'g' => 'g'
				]
			]
		];

		$result = Parkour::set($data, 'a', 'a');
		$result = Parkour::set($result, 'b.d.e', 'e');
		$result = Parkour::set($result, 'b.f.g', 'g');

		$this->assertEquals($expected, $result);
	}

	/**
	 *
	 */
	public function testUpdate() {
		$data = [
			'a' => 1,
			'b' => [
				'c' => 2,
				'd' => [
					'e' => 3
				]
			]
		];

		$expected = [
			'a' => 2,
			'b' => [
				'c' => 2,
				'd' => [
					'e' => 4
				]
			]
		];

		$increment = function($value) {
			return $value + 1;
		};

		$result = Parkour::update($data, 'a', $increment);
		$result = Parkour::update($result, 'z', $increment);
		$result = Parkour::update($result, 'b.d.e', $increment);

		$this->assertEquals($expected, $result);
	}

	/**
	 *
	 */
	public function testSplitPath() {
		$this->assertEquals(
			['a', 'b'],
			Parkour::splitPath(['a', 'b'])
		);
	}

	/**
	 *
	 */
	public function testSplitDottedPath() {
		$this->assertEquals(
			['a', 'b'],
			Parkour::splitPath('a.b')
		);
	}

	/**
	 *
	 */
	public function testSplitEmptyPath() {
		$this->setExpectedException('InvalidArgumentException');
		Parkour::splitPath([]);
	}

	/**
	 *
	 */
	public function testSplitEmptyDottedPath() {
		$this->setExpectedException('InvalidArgumentException');
		Parkour::splitPath('');
	}

	/**
	 *
	 */
	public function testSplitInvalidPath() {
		$this->setExpectedException('InvalidArgumentException');
		Parkour::splitPath(12);
	}
}
