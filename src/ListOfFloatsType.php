<?php declare(strict_types=1);

namespace Attitude\Types;

class ListOfFloatsType extends AbstractListType {
  public function parse($item): float {
    if (is_float($item)) {
      return $item;
    } else {
      throw new \TypeError(static::getUnexpectedTypeErrorMessage('float', $item));
    }
  }
}
