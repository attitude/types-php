# Types for PHP

PHP types powered by Reflections

> [!WARNING] > **Experimental project – feedback is appreciated**

## Features:

- [x] Validation
- [x] Destructuring
- [x] `AbstractListType` methods inspired by JavaScript's `Array` class (incomplete)
- [x] Recursive types validation (needs more testing)
- [x] Primitive lists:
  - [ListOfBooleansType](src/ListOfBooleansType.php)
  - [ListOfFloatsType](src/ListOfFloatsType.php)
  - [ListOfIntegersType](src/ListOfIntegersType.php)
  - [ListOfMixedType](src/ListOfMixedType.php)
  - [ListOfNumbersType](src/ListOfNumbersType.php)
  - [ListOfNumericValuesType](src/ListOfNumericValuesType.php)
  - [ListOfStringsType](src/ListOfStringsType.php)

## Installation

> [!IMPORTANT]
> This project is not yet published to Packagist. You need to add the repository manually or clone the repository as a submodule.

### Option 1: Add as a Git submodule

```shell
$ git submodule add git@github.com:attitude/types-php.git path/to/types-php
```

### Option 2: Add as a dependency using Composer

Update `composer.json` of your project:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/attitude/types-php"
        }
    ],
    "require": {
        "attitude/types": "dev-main"
    }
}
```

```shell
$ composer install
```

### Option 3: Download the repository as a ZIP

---

## Usage

### Shape types

Define a class extending `AbstractShapeType` and define properties with types
just like you would with a regular class.

When creating an instance of the class, you can pass an associative array of
properties. The constructor will validate the properties and throw an exception
if any of the properties are missing or have the wrong type.

Unless you define const REST, the constructor will throw an exception if there
are any properties in the array that are not defined in the class.

The value of the REST constant will be used as a key for all properties that
are not defined in the class.

```php
<?php

require_once 'path/to/types-php/src/Types.php';

class HeaderProps extends AbstractShapeType {
  public const REST = '__';
  public array $__;
  public string $title;
  public ?string $subtitle;
}

$header = new HeaderProps([
  'title' => 'Title',
  'extra' => 'Extra',
]);

['title' => $title, '__' => $rest] = $header;

$header; // instanceof HeaderProps::class
$title;  // 'Title'
$rest;   // ['extra' => 'Extra']
```

### List types

To define a list of items with a specific type, use `AbstractListType`
and provide a `parse()` method that will be used to parse each item.

```php
<?php

require_once 'path/to/types-php/src/Types.php';

class HeadersList extends AbstractListType {
  public function parse($item): HeaderProps {
    if ($item instanceof HeaderProps) {
      return $item;
    } else {
      return new HeaderProps($item);
    }
  }
}
```

The `AbstractListType` class has few methods to help you work with the list similar
to JavaScript's `Array` class.

```php
<?php

$items = new HeadersList([
  ['title' => 'Title 1'],
  ['title' => 'Title 2'],
]);

$items->length; // 2
$items->push(new HeaderProps(['title' => 'Title 3']));

$items->map(function ($item) {
  return $item->title;
}); // ['Title 1', 'Title 2', 'Title 3']
```

---

_Enjoy!_

Created by [martin_adamko](https://www.threads.net/@martin_adamko)
