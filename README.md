Parkour
=======

[![Build status](http://img.shields.io/travis/felixgirault/parkour/master.svg?style=flat-square)](http://travis-ci.org/felixgirault/parkour)
[![Scrutinizer Code Quality](http://img.shields.io/scrutinizer/g/felixgirault/parkour/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/felixgirault/parkour/?branch=master)
[![Code Coverage](http://img.shields.io/scrutinizer/coverage/g/felixgirault/parkour/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/felixgirault/parkour/?branch=master)

A collection of utilities to manipulate arrays.

The aim of this library is to provide a consistent API, unlike the one natively implemented in PHP.

API
---

```php
use Parkour\Parkour as _;
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
[every()](#every),
[combine()](#combine),
[normalize()](#normalize),
[reindex()](#reindex),
[merge()](#merge),
[range()](#range),
[has()](#has),
[get()](#get),
[set()](#set),
[update()](#update).

### each()

```php
_::each(['foo' => 'bar'], function($value, $key) {
	echo "$key: $value";
});

// foo: bar
```

### map()

```php
_::map([1, 2], function($value, $key) {
	return $value * 2;
});

// [2, 4]
```

### mapKeys()

```php
_::mapKeys([1, 2], function($value, $key) {
	return "key-$key";
});

// ['key-0' => 2, 'key-1' => 4]
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

### reject()

```php
$data = [
	'a' => 1,
	'b' => 2
];

_::reject($data, function($value, $key) {
	return $value === 1;
});

// ['b' => 2]
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

### find()

```php
_::find([1, 2], function($value, $key) {
	return $key === 1;
});

// 2
```

### findKey()

```php
_::findKey([1, 2], function($value, $key) {
	return $value === 2;
});

// 1
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

### normalize()

```php
$data = [
	'foo' => 'bar'
	'baz'
];

_::normalize($data, true);

// ['foo' => 'bar', 'baz' => true]
```

### reindex()

```php
$data = ['foo' => 'bar'];

_::reindex($data, [
	'foo' => 'baz'
]);

// ['baz' => 'bar']
```
