<?php declare(strict_types=1);

namespace Attitude\Types;

class ListOfBooleansType extends AbstractListType {
  public function parse($item): bool {
    if (is_bool($item)) {
      return $item;
    } else {
      throw new \TypeError(static::getUnexpectedTypeErrorMessage('bool', $item));
    }
  }
}
