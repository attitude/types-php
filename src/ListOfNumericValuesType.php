<?php declare(strict_types=1);

namespace Attitude\Types;

class ListOfNumericValuesType extends AbstractListType {
  public function parse($item): int|float {
    if (is_int($item)) {
      return $item;
    } else if (is_float($item)) {
      return $item;
    } else if (is_numeric($item)) {
      return (int) $item;
    } else {
      throw new \TypeError(static::getUnexpectedTypeErrorMessage('numeric', $item));
    }
  }
}
