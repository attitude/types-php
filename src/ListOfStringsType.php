<?php declare(strict_types=1);

namespace Attitude\Types;

 class ListOfStringsType extends AbstractListType {
  public function parse($item): string {
    if (is_string($item)) {
      return $item;
    } else {
      throw new \TypeError(static::getUnexpectedTypeErrorMessage('string', $item));
    }
  }
}
