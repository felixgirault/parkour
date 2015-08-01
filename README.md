Parkour
=======

[![Build status](http://img.shields.io/travis/felixgirault/parkour/master.svg?style=flat-square)](http://travis-ci.org/felixgirault/parkour)
[![Scrutinizer Code Quality](http://img.shields.io/scrutinizer/g/felixgirault/parkour/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/felixgirault/parkour/?branch=master)
[![Code Coverage](http://img.shields.io/scrutinizer/coverage/g/felixgirault/parkour/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/felixgirault/parkour/?branch=master)

A collection of utilities to manipulate arrays.

The aim of this library is to provide a consistent API, unlike the one natively implemented in PHP.

Examples
--------

Using your own functions:

```php
Parkour\Iterate::filter([5, 15, 20], function($value) {
	return $value > 10;
});

// [15, 20]
```

Using some of the built-in functors:

```php
Parkour\Iterate::filter([5, 15, 20], new Parkour\Functor\Greater(10));
// [15, 20]

Parkour\Iterate::map([10, 20], new Parkour\Functor\Multiply(2), 0);
// [20, 40]

Parkour\Iterate::reduce([10, 20], new Parkour\Functor\Add(), 0);
// 30

```

API
---

### Iterate

```php
use Parkour\Iterate;
```

[each()](#each),
[map()](#map),
[mapKeys()](#mapKeys),
[filter()](#filter),
[reject()](#reject),
[reduce()](#reduce),
[find()](#find),
[findKey()](#findKey),
[some()](#some),
[every()](#every).

#### each()

```php
Iterate::each(['foo' => 'bar'], function($value, $key) {
	echo "$key: $value";
});

// foo: bar
```

#### map()

```php
$data = [
	'foo' => 1,
	'bar' => 2
];

Iterate::map($data, function($value, $key) {
	return $value * 2;
});

// [
// 	'foo' => 2,
// 	'bar' => 4
// ]
```

#### mapKeys()

```php
$data = [
	'foo' => 1,
	'bar' => 2
];

Iterate::map($data, function($value, $key) {
	return strtoupper($key);
});

// [
// 	'FOO' => 1,
// 	'BAR' => 2
// ]
```

#### filter()

```php
$data = [
	'foo' => true,
	'bar' => false
];

Iterate::filter($data, function($value, $key) {
	return $value === true;
});

// [
// 	'foo' => true
// ]
```

#### reject()

```php
$data = [
	'foo' => true,
	'bar' => false
];

Iterate::reject($data, function($value, $key) {
	return $value === true;
});

// [
// 	'bar' => false
// ]
```

#### reduce()

```php
Iterate::reduce([1, 2], function($memo, $value, $key) {
	return $memo + $value;
}, 0);

// 3
```

Using built-in functors:

```php
Iterate::reduce([1, 2], new Parkour\Functor\Add(), 0); // 3
Iterate::reduce([2, 2], new Parkour\Functor\Mutiply(), 2); // 8
```

#### find()

```php
$data = [
	'foo' => 'PHP',
	'bar' => 'JavaScript'
];

Iterate::find($data, function($value, $key) {
	return $key === 'foo';
});

// 'PHP'
```

#### findKey()

```php
$data = [
	'foo' => 'PHP',
	'bar' => 'JavaScript'
];

Iterate::findKey($data, function($value, $key) {
	return $value === 'PHP';
});

// 'foo'
```

#### some()

```php
Iterate::some([5, 10, 20], function($value, $key) {
	return $value > 10;
});

// true
```

Using a built-in functor:

```php
Iterate::some([1, 2], new Parkour\Functor\AlwaysFalse()); // false
```

#### every()

```php
Iterate::every([1, 2], function($value, $key) {
	return $value === 1;
});

// false
```

Using a built-in functor:

```php
Iterate::every([1, 2], new Parkour\Functor\AlwaysTrue()); // true
```

### Transform

```php
use Parkour\Transform;
```

[combine()](#combine),
[normalize()](#normalize),
[reindex()](#reindex),
[merge()](#merge).

#### combine()

```php
$data = [
	['id' => 12, 'name' => 'foo'],
	['id' => 37, 'name' => 'bar']
];

Transform::combine($data, function($row, $key) {
	yield $row['id'] => $row['name'];
});

// [
// 	12 => 'foo',
// 	37 => 'bar'
// ]
```

#### normalize()

```php
$data = [
	'foo' => 'bar'
	'baz'
];

Transform::normalize($data, true);

// [
// 	'foo' => 'bar',
// 	'baz' => true
// ]
```

#### reindex()

```php
$data = ['foo' => 'bar'];

Transform::reindex($data, [
	'foo' => 'baz'
]);

// [
// 	'baz' => 'bar'
// ]
```

#### merge()

```php
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

Transform::merge($first, $second);

// [
// 	'one' => 1,
// 	'two' => 'two',
// 	'three' => [
// 		'four' => 'four',
// 		'five' => 5
// 	]
// ]
```

### Access

```php
use Parkour\Access;
```

[has()](#has),
[get()](#get),
[set()](#set),
[update()](#update).

#### has()

```php
$data = [
	'a' => 'foo',
	'b' => [
		'c' => 'bar'
	]
];

Access::has($data, 'b.c'); // true
Access::has($data, ['b', 'c']); // true
```

#### get()

```php
$data = [
	'a' => 'foo',
	'b' => [
		'c' => 'bar'
	]
];

Access::get($data, 'a'); // 'foo'
Access::get($data, 'b.c'); // 'bar'
Access::get($data, ['b', 'c']); // 'bar'
```

#### set()

```php
$data = [
	'a' => 'foo',
	'b' => [
		'c' => 'bar'
	]
];

$data = Access::set($data, 'a', 'a');
$data = Access::set($data, 'b.c', 'c');
$data = Access::set($data, ['b', 'd'], 'd');

// [
// 	'a' => 'a',
// 	'b' => [
// 		'c' => 'c',
// 		'd' => 'd'
// 	]
// ]
```

#### update()

```php
$data = [
	'a' => 'foo',
	'b' => [
		'c' => 'bar'
	]
];

$data = Access::update($data, 'a', function($value) {
	return strtoupper($value);	
});

$data = Access::update($data, 'b.c', function($value) {
	return $value . $value;
});

$data = Access::update($data, ['b', 'd'], 'd');

// [
// 	'a' => 'FOO',
// 	'b' => [
// 		'c' => 'barbar'
// 	]
// ]
```

Functors
--------

`Add`,
`AlwaysFalse`,
`AlwaysTrue`,
`Cunjunct`,
`Disjunct`,
`Divide`,
`Equal`,
`Greater`,
`GreaterOrEqual`,
`Identical`,
`Identity`,
`Lower`,
`LowerOrEqual`,
`Multiply`,
`NotEqual`,
`NotIdentical`,
`Substract`.

The vast majority of these functors can be used in two different ways.

Without any configuration:

```php
$Add = new Parkour\Functor\Add();
Iterate::reduce([10, 20], $Add, 0);

// is equivalent to:
Iterate::reduce([10, 20], function($memo, $value) {
	return $memo + $value;
}, 0);
```

Or with a fixed parameter:

```php
$Add = new Parkour\Functor\Add(5);
Iterate::map([10, 20], $Add, 0);

// is equivalent to:
Iterate::map([10, 20], function($value) {
	return $value + 5;
}, 0);
```
