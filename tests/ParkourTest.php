<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */
namespace Parkour;

use PHPUnit_Framework_TestCase as TestCase;



/**
 *
 */
class ParkourTest extends TestCase {

	/**
	 *
	 */
	public $Closures = null;



	/**
	 *
	 */
	public function setUp() {
		$this->Closures = $this->getMock('stdClass', [
			'closure'
		]);
	}



	/**
	 *
	 */
	public function testMap() {
		$this->Closures
			->expects($this->any())
			->method('closure')
			->will($this->returnValueMap([
				[1, 'a', 2],
				[2, 'b', 4]
			]));

		$data = [
			'a' => 1,
			'b' => 2
		];

		$mapped = Parkour::map($data, function($value, $key) {
			return $this->Closures->closure($value, $key);
		});

		$this->assertEquals([
			'a' => 2,
			'b' => 4
		], $mapped);
	}



	/**
	 *
	 */
	public function testReduceCalls() {
		$this->Closures
			->expects($this->any())
			->method('closure')
			->will($this->returnValueMap([
				[0, 1, 0, 1],
				[1, 2, 1, 3]
			]));

		$data = [1, 2];
		$reduced = Parkour::reduce($data, function($memo, $value, $key) {
			return $this->Closures->closure($memo, $value, $key);
		}, 0);

		$this->assertEquals(3, $reduced);
	}



	/**
	 *
	 */
	public function testFilter() {
		$this->Closures
			->expects($this->any())
			->method('closure')
			->will($this->returnValueMap([
				[1, 'a', false],
				[2, 'b', true]
			]));

		$data = [
			'a' => 1,
			'b' => 2
		];

		$filtered = Parkour::filter($data, function($value, $key) {
			return $this->Closures->closure($value, $key);
		});

		$this->assertEquals([
			'b' => 2
		], $filtered);
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

		// overwriting existing names
		$indexed = Parkour::combine($users, function($user) {
			yield $user['name'] => $user['id'];
		});

		$this->assertEquals([
			'a' => 1,
			'b' => 3
		], $indexed);

		// not overwriting existing names
		$indexed = Parkour::combine($users, function($user) {
			yield $user['name'] => $user['id'];
		}, false);

		$this->assertEquals([
			'a' => 1,
			'b' => 2
		], $indexed);
	}



	/**
	 *
	 */
	public function testInvoke() {
		$this->Closures
			->expects($this->once())
			->method('closure')
			->with(
				$this->equalTo('b'),
				$this->equalTo('a')
			);

		$data = ['a' => 'b'];

		Parkour::invoke($data, function($value, $key) {
			$this->Closures->closure($value, $key);
		});
	}
}
