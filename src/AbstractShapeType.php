<?php declare(strict_types=1);

namespace Attitude\Types;
abstract class AbstractShapeType implements \ArrayAccess, \Iterator, \JsonSerializable {
  protected array $keys = [];

  /**
   * Constructs a new instance of the AbstractShapeType class.
   *
   * @param \stdClass|array $array An array or stdClass object containing the properties of the shape.
   *
   * @throws \Exception If extra properties are found in the $array parameter.
   * @throws \Exception If a required property is not found in the $array parameter.
   * @throws \Exception If an unsupported type is encountered in the $array parameter.
   */
  public function __construct(\stdClass|array $array = []) {
    $shape = (array) $array;

    $reflection = new \ReflectionClass($this);
    $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
    $constants = $reflection->getConstants();

    // Throw an error when passing more properties than expected
    $shapeKeys = array_keys($shape);
    $propertyNames = array_column($properties, 'name');
    $extraKeys = array_diff($shapeKeys, $propertyNames);
    $restPropertyName = isset($constants) && array_key_exists('REST', $constants) && $constants['REST'] && is_string($constants['REST']) ? $constants['REST'] : null;

    if ($restPropertyName) {
      if (in_array($constants['REST'], $propertyNames)) {
        $restProperty = $reflection->getProperty($constants['REST']);
        $restProperty->setValue($this, array_intersect_key($shape, array_flip($extraKeys)));
      } else {
        throw new \Exception(static::class." is missing public property `\${$constants['REST']}` to store extra properties");
      }
    } else {
      if (count($extraKeys) > 0) {
        throw new \Exception(static::class." expects only declared properties, got: " . implode(', ', $extraKeys));
      }
    }

    foreach ($properties as $property) {
      assert($property instanceof \ReflectionProperty);

      if ($property->getName() === $restPropertyName) {
        continue;
      }

      $name = $property->getName();
      $this->keys[] = $name;

      $type = $property->getType();
      assert($type instanceof \ReflectionType);

      if (!array_key_exists($name, $shape)) {
        if ($type->allowsNull()) {
          $property->setValue($this, null);
        } else {
          throw new \Exception(static::class." expects property {$name} to be set");
        }
      } else {
        $value = $shape[$name];
        static::assert($name, $value);

        if (is_scalar($value) || is_null($value)) {
          $property->setValue($this, $value);
        } else if (is_array($value)) {
          if ($type instanceof \ReflectionNamedType) {
            $typeName = $type->getName();
            $valueType = gettype($value);

            if ($typeName === $valueType) {
              $property->setValue($this, $value);
            } else if ($typeName === 'null') {
              if ($value === null) {
                $property->setValue($this, null);
              } else {
                throw new \TypeError(static::class." expects `{$name}` property to be null, got {$valueType} instead");
              }
            } else if ($typeName === 'mixed') {
              $property->setValue($this, $value);
            } else {
              $property->setValue($this, new $typeName($value));
            }
          } else if ($type instanceof \ReflectionUnionType) {
            $types = $type->getTypes();
            $typeNames = array_map(fn($type) => $type->getName(), $types);
            $valueType = gettype($value);

            if (in_array($valueType, $typeNames)) {
              $property->setValue($this, $value);
            } else {
              $nonScalarTypeNames = array_filter($typeNames, fn($typeName) => match($typeName) {
                'int', 'float', 'string', 'bool', 'null' => false,
                default => true,
              });

              if ($valueType === 'array' && in_array('array', $nonScalarTypeNames)) {
                $property->setValue($this, $value);
              } else if ($valueType === 'object' && in_array('stdClass', $nonScalarTypeNames)) {
                $property->setValue($this, $value);
              } else {
                $classes = array_filter($nonScalarTypeNames, fn($typeName) => match($typeName) {
                  'array', 'stdClass' => false,
                  default => true,
                });

                $instance = null;

                foreach ($classes as $className) {
                  if ($instance === null) {
                    try {
                      $instance = new $className($value);
                    } catch (\Exception $e) {
                      continue;
                    }
                  }
                }

                if ($instance !== null) {
                  $property->setValue($this, $instance);
                } else {
                  throw new \Exception("Unsupported type: " . gettype($value));
                }
              }
            }
          } else {
            throw new \Exception("Array expected, got: " . get_class($type));
          }
        } else if (is_object($value)) {
          $property->setValue($this, $value);
        } else {
          throw new \Exception("Unsupported type: " . gettype($value));
        }
      }
    }
  }

  static protected function assert(string $key, mixed $value): void {}

  public function keys(): array {
    return $this->keys;
  }

  public function values(): array {
    return array_map(fn(string $key) => $this->{$key}, $this->keys);
  }

  public function entries(): array {
    $keys = $this->keys();
    $values = $this->values();

    return array_map(fn($key, $value) => [$key, $value], $keys, $values);
  }

  public function fromEntries(array $entries): static {
    $shape = [];

    foreach ($entries as $entry) {
      [$key, $value] = $entry;
      $shape[$key] = $value;
    }

    return new static($shape);
  }

  // ArrayAccess::offsetExists
  public function offsetExists($offset): bool {
    return isset($this->$offset);
  }

  // ArrayAccess::offsetGet
  public function offsetGet($offset): mixed {
    return $this->$offset;
  }

  // ArrayAccess::offsetSet
  public function offsetSet($offset, $value): void {
    $this->$offset = $value;
  }

  // ArrayAccess::offsetUnset
  public function offsetUnset($offset): void {
    unset($this->$offset);
  }

  // JsonSerializable::jsonSerialize
  public function jsonSerialize(): array {
    return array_combine($this->keys(), $this->values());
  }

  // implement abstract methods:
  function current(): mixed {
    $key = current($this->keys);

    return $this->{$key};
  }

  function key(): mixed {
    return $this->keys[key($this->keys)];
  }

  function next(): void {
    next($this->keys);
  }

  function rewind(): void {
    reset($this->keys);
  }

  function valid(): bool {
    return key($this->keys) !== null;
  }
}
