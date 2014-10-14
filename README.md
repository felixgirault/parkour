Parkour
=======

[![Build status](https://travis-ci.org/felixgirault/parkour.svg?branch=master)](http://travis-ci.org/felixgirault/parkour)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/felixgirault/parkour/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/felixgirault/parkour/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/felixgirault/parkour/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/felixgirault/parkour/?branch=master)

A collection of utilities to manipulate arrays.

The aim of this library is to provide a consistent API, unlike the one natively implemented in PHP.

API
---

```php
use Parkour\Parkour as _;
```

[map()](#map),
[reduce()](#reduce),
[mapReduce()](#mapreduce),
[every()](#every),
[some()](#some),
[firstOk()](#firstok),
[firstNotOk()](#firstnotok),
[filter()](#filter),
[passing()](#passing),
[combine()](#combine),
[each()](#each),
[reindex()](#reindex),
[normalize()](#normalize).

### map()

```php
_::map([1, 2], function($value, $key) {
	return $value * 2;
});

// [2, 4]
```

### reduce()

```php
_::reduce([1, 2], function($memo, $value, $key) {
	return $memo + $value;
}, 0);

// 3

_::reduce([1, 2], new Parkour\Functor\Add(), 0);

// 3

_::reduce([2, 2], new Parkour\Functor\Mutiply(), 2);

// 8
```

### mapReduce()

```php
$map = function($value, $key) {
	return $value * 2;
};

$reduce = function($memo, $value, $key) {
	return $memo + $value;
};

_::mapReduce([1, 2], $map, $reduce, 0);

// 6
```

### every()

```php
_::every([1, 2], function($value, $key) {
	return $value === 1;
});

// false

_::every([1, 2], function($value, $key) {
	return true;
});

// true
```

### some()

```php
_::some([1, 2], function($value, $key) {
	return false;
});

// false

_::some([1, 2], function($value, $key) {
	return $value === 1;
});

// true
```

### firstOk()

```php
$data = [
	'a' => 1,
	'b' => 2
];

_::firstOk($data, function($value, $key) {
	return true;
});

// 'a'
```

### firstNotOk()

```php
$data = [
	'a' => 1,
	'b' => 2
];

_::firstOk($data, function($value, $key) {
	return false;
});

// 'a'
```

### filter()

```php
$data = [
	'a' => 1,
	'b' => 2
];

_::filter($data, function($value, $key) {
	return $value === 1;
});

// ['a' => 1]
```

### passing()

```php
_::passing([1, 2], function($value, $key) {
	return $value === 1;
});

// [1]
```

### combine()

```php
$data = [
	['key' => 1, 'value' => 'foo'],
	['key' => 2, 'value' => 'bar']
];

_::combine($data, function($row, $key) {
	yield $row['key'] => $row['value'];
});

// [1 => 'foo', 2 => 'bar']
```

### each()

```php
_::each(['foo' => 'bar'], function($value, $key) {
	echo "$key: $value";
});

// foo: bar
```

### reindex()

```php
$data = ['foo' => 'bar'];

_::reindex($data, [
	'foo' => 'baz'
]);

// ['baz' => 'bar']
```

### normalize()

```php
$data = [
	'foo' => 'bar'
	'baz'
];

_::normalize($data, true);

// ['foo' => 'bar', 'baz' => true]
```
