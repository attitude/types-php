<?php declare(strict_types=1);

namespace Attitude\Types;

require_once 'AbstractListType.php';

describe('ListType', function () {
  it('should validate custom List class', function () {
    expect(new ListOfIntegersType([1, 2, 3]))->toBeInstanceOf(ListOfIntegersType::class);
  });

  it('should throw an error when an unsupported type is encountered', function () {
    expect(fn() => new ListOfIntegersType([1, '2', 3]))->toThrow('expects members to be of type int, got string');
  });

  it('should validate custom List class with numeric types', function () {
    expect(new ListOfNumericValuesType([1, 2.5, 3, '4']))->toBeInstanceOf(ListOfNumericValuesType::class);
  });

  it('should allow destructuring', function () {
    $list = new ListOfIntegersType([1, 2, 3]);
    [$first, $second, $third] = $list;

    expect($first)->toBe(1);
    expect($second)->toBe(2);
    expect($third)->toBe(3);
  });

  it('should allow array access', function () {
    $list = new ListOfIntegersType([1, 2, 3]);

    expect($list[0])->toBe(1);
    expect($list[1])->toBe(2);
    expect($list[2])->toBe(3);
  });

  it('should filter list', function () {
    $list = new ListOfIntegersType([1, 2, 3]);

    expect($list->filter(fn($item) => $item > 1))->toBe([2, 3]);
  });

  it('should find in list', function () {
    $list = new ListOfIntegersType([1, 2, 3]);

    expect($list->find(fn($item) => $item > 1))->toBe(2);
  });

  it('should flat map list', function () {
    $list = new ListOfIntegersType([1, 2, 3]);

    expect($list->flatMap(fn($item) => [$item, $item * 2]))->toBe([1, 2, 2, 4, 3, 6]);
  });

  it('should loop through list', function () {
    $list = new ListOfIntegersType([1, 2, 3]);
    $result = [];
    $list->forEach(function($item, $index) use (&$result) {
      $result[] = [$index, $item];
    });

    expect($result)->toBe([[0, 1], [1, 2], [2, 3]]);
  });

  it('should return index of item', function () {
    $list = new ListOfIntegersType([1, 2, 3, 4, 5, 6]);

    expect($list->indexOf(2))->toBe(1);
    expect($list->indexOf(7))->toBe(-1);
    expect($list->indexOf(4, 3))->toBe(3);
  });

  it('should check if item is included', function () {
    $list = new ListOfIntegersType([1, 2, 3]);

    expect($list->includes(2))->toBeTrue();
    expect($list->includes(4))->toBeFalse();
  });

  it('should join list', function () {
    $list = new ListOfIntegersType([1, 2, 3]);

    expect($list->join(', '))->toBe('1, 2, 3');
  });

  it('shoud return keys', function () {
    $list = new ListOfIntegersType([1, 2, 3]);

    expect($list->keys())->toBe([0, 1, 2]);
  });

  it('should get length of list', function () {
    $list = new ListOfIntegersType([1, 2, 3]);

    expect($list->length)->toBe(3);
  });

  it('should map list items', function () {
    $list = new ListOfIntegersType([1, 2, 3]);

    expect($list->map(fn($item) => $item * 2))->toBe([2, 4, 6]);
  });

  it('should pop list item', function () {
    $list = new ListOfIntegersType([1, 2, 3]);

    expect($list->pop())->toBe(3);
    expect((array) $list)->toBe([1, 2]);
  });

  it('should reduce list items', function () {
    $list = new ListOfIntegersType([1, 2, 3]);

    expect($list->reduce(fn($carry, $item) => $carry + $item, 0))->toBe(6);
  });

  it('should reverse list', function () {
    $list = new ListOfIntegersType([1, 2, 3]);

    expect((array) $list->reverse())->toBe([3, 2, 1]);
  });

  it('should shift list item', function () {
    $list = new ListOfIntegersType([1, 2, 3]);

    expect($list->shift())->toBe(1);
    expect((array) $list)->toBe([2, 3]);
  });

  it('should slice list', function () {
    $list = new ListOfIntegersType([1, 2, 3]);

    expect((array) $list->slice(1))->toBe([2, 3]);
    expect((array) $list->slice(1, 1))->toBe([2]);
  });

  it('should unshift list item', function () {
    $list = new ListOfIntegersType([1, 2, 3]);

    expect($list->unshift(0))->toBe(4);
    expect((array) $list)->toBe([0, 1, 2, 3]);
  });

  it('should unwrap values', function () {
    $list = new ListOfIntegersType([1, 2, 3]);

    expect($list->values())->toBe([1, 2, 3]);
  });

  it('should clone list with new value at index', function () {
    $list = new ListOfIntegersType([1, 2, 3]);
    $newList = $list->with(1, 4);

    expect($newList)->toBeInstanceOf(ListOfIntegersType::class);
    expect((array) $newList)->toBe([1, 4, 3]);
    expect($list === $newList)->toBeFalse();
  });
});
