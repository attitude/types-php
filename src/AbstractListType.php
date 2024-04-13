<?php declare(strict_types=1);

namespace Attitude\Types;

abstract class AbstractListType extends \ArrayObject implements \JsonSerializable {
  protected static function getUnexpectedTypeErrorMessage(string $expected, mixed $item): string {
    return static::class.' expects members to be of type '.$expected.', got '.gettype($item);
  }

  public function __get(string $name): mixed {
    if ($name === 'length') {
      return count($this);
    }

    return null;
  }

  /**
   * Constructs a new instance of the ListType class.
   *
   * @param array $array An array containing the list items.
   */
  public function __construct(array $array = []) {
    parent::__construct(array_map([$this, 'parse'], $array));
  }

  public function __toString(): string {
    return $this->join(', ');
  }

  abstract protected function parse($item): mixed;

  public function filter(callable $callback): array {
    $copy = [];

    foreach ($this as $index => $value) {
      if ($callback($value, $index, $this)) {
        $copy[] = $value;
      }
    }

    return $copy;
  }

  public function find(callable $callback): mixed {
    foreach ($this as $index => $value) {
      if ($callback($value, $index, $this)) {
        return $this->parse($value);
      }
    }

    return null;
  }

  public function flatMap(callable $callback): array {
    $copy = [];

    foreach ($this as $index => $value) {
      $copy = array_merge($copy, (array) $callback($value, $index, $this));
    }

    return $copy;
  }

  public function forEach(callable $callback): void {
    foreach ($this as $index => $value) {
      $callback($value, $index, $this);
    }
  }

  public function includes(mixed $item, int $fromIndex = null): bool {
    return $this->indexOf($item, $fromIndex) !== -1;
  }

  public function indexOf(mixed $item, int $fromIndex = null): int {
    if ($fromIndex === null) {
      $index = array_search($item, (array) $this, true);

      if ($index === false) {
        return -1;
      } else {
        return $index;
      }
    } else {
      if ($fromIndex < 0) {
        $fromIndex += $this->count();
      }

      if ($fromIndex < $this->count()) {
        $index = $this->slice($fromIndex)->indexOf($item);

        if ($index === -1) {
          return -1;
        } else {
          return $index + $fromIndex;
        }
      } else {
        return -1;
      }
    }
  }

  public function join(string $separator = ','): string {
    return implode($separator, (array) $this);
  }

  public function keys(): array {
    return array_keys((array) $this);
  }

  public function map(callable $callback): array {
    $copy = [];

    foreach ($this as $index => $value) {
      $copy[] = $callback($value, $index, $this);
    }

    return $copy;
  }

  public function pop(): mixed {
    $popped = $this[count($this) - 1];
    $this->offsetUnset(count($this) - 1);

    return $popped;
  }

  public function push(...$items): int {
    return array_push($this, ...array_map([$this, 'parse'], $items));
  }

  public function reduce(callable $callback, mixed $initial = null): mixed {
    $accumulator = $initial;

    foreach ($this as $index => $value) {
      $accumulator = $callback($accumulator, $value, $index, $this);
    }

    return $accumulator;
  }

  public function reverse(): static {
    $all = array_reverse((array) $this);
    $this->exchangeArray($all);

    return $this;
  }

  public function shift(): mixed {
    $all = (array) $this;
    $shifted = array_shift($all);
    $this->exchangeArray($all);

    return $shifted;
  }

  public function slice(int $offset, int $length = null): static {
    return new static(array_slice((array) $this, $offset, $length));
  }

  public function unshift(...$items): int {
    $this->exchangeArray([...array_map([$this, 'parse'], $items), ...$this]);

    return $this->count();
  }

  public function values(): array {
    return array_values((array) $this);
  }

  public function with(int $index, mixed $value): static {
    $copy = clone $this;
    $copy->offsetSet($index, $value);

    return $copy;
  }

  // JsonSerializable::jsonSerialize
  public function jsonSerialize(): array {
    return (array) $this;
  }
}
