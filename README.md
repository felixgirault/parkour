Parkour
=======

[![Build status](https://travis-ci.org/felixgirault/parkour.svg?branch=master)](http://travis-ci.org/felixgirault/parkour)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/felixgirault/parkour/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/felixgirault/parkour/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/felixgirault/parkour/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/felixgirault/parkour/?branch=master)

A collection of utilities to manipulate arrays.

The aim of this library is to provide a consistent API, unlike the one natively implemented in PHP.

API
---

### map()

```php
Parkour::map([1, 2], function($value, $key) {
	return $value * 2;
});

// [2, 4]
```

### reduce()

```php
Parkour::reduce([1, 2], function($memo, $value, $key) {
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

Parkour::mapReduce([1, 2], $map, $reduce, 0);

// 6
```

### allOk()

```php
Parkour::allOk([1, 2], function($value, $key) {
	return $value === 1;
});

// false

Parkour::allOk([1, 2], function($value, $key) {
	return true;
});

// true
```

### oneOk()

```php
Parkour::oneOk([1, 2], function($value, $key) {
	return false;
});

// false

Parkour::oneOk([1, 2], function($value, $key) {
	return $value === 1;
});

// true
```

### filter()

```php
Parkour::filter([1, 2], function($value, $key) {
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

Parkour::combine($data, function($row, $key) {
	yield $row['key'] => $row['value'];
});

// [1 => 'foo', 2 => 'bar']
```

### invoke()

```php
Parkour::invoke(['foo' => 'bar'], function($value, $key) {
	echo "$key: $value";
});

// foo: bar
```

### reindex()

```php
$data = ['foo' => 'bar'];

Parkour::reindex($data, [
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

Parkour::normalize($data, true);

// ['foo' => 'bar', 'baz' => true]
```
