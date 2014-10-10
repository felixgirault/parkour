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

### allOk()

```php
_::allOk([1, 2], function($value, $key) {
	return $value === 1;
});

// false

_::allOk([1, 2], function($value, $key) {
	return true;
});

// true
```

### oneOk()

```php
_::oneOk([1, 2], function($value, $key) {
	return false;
});

// false

_::oneOk([1, 2], function($value, $key) {
	return $value === 1;
});

// true
```

### filter()

```php
_::filter([1, 2], function($value, $key) {
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

### invoke()

```php
_::invoke(['foo' => 'bar'], function($value, $key) {
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
