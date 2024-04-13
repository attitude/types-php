<?php declare(strict_types=1);

namespace Attitude\Types;

class ListOfMixedType extends AbstractListType {
  public function parse($item): mixed {
    return $item;
  }
}
