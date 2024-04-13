<?php declare(strict_types=1);

namespace Attitude\Types;

class ListOfIntegersType extends AbstractListType {
  public function parse($item): int {
    if (is_int($item)) {
      return $item;
    } else {
      throw new \TypeError(static::getUnexpectedTypeErrorMessage('int', $item));
    }
  }
}
