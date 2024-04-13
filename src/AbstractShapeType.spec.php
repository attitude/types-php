<?php declare(strict_types=1);

use Attitude\Types\AbstractShapeType;

class HeaderProps extends AbstractShapeType {
  public string $title;
  public ?string $subtitle;
}

class HeaderPropsWithRest extends AbstractShapeType {
  public const REST = '__';
  public array $__;
  public string $title;
  public ?string $subtitle;
}

class HeaderPropsWithMissingRestProperty extends AbstractShapeType {
  public const REST = '__';
  public string $title;
  public ?string $subtitle;
}

describe('ShapeType', function () {
  it('should validate custom Shape class', function () {
    expect(new HeaderProps(['title' => 'Title']))->toBeInstanceOf(HeaderProps::class);
    expect(new HeaderProps(['title' => 'Title', 'subtitle' => 'Subtitle']))->toBeInstanceOf(HeaderProps::class);
  });

  it('should throw an error when a required property is missing', function () {
    expect(fn() => new HeaderProps([]))->toThrow('expects property title to be set');
  });

  it('should throw an error when extra properties are found', function () {
    expect(fn() => new HeaderProps(['title' => 'Title', 'extra' => 'Extra']))->toThrow('expects only declared properties, got: extra');
  });

  it('should accept extra properties when using REST const', function () {
    $header = new HeaderPropsWithRest([
      'title' => 'Title',
      'extra' => 'Extra',
    ]);

    ['title' => $title, '__' => $rest] = $header;

    expect($header)->toBeInstanceOf(HeaderPropsWithRest::class);
    expect($title)->toBe('Title');
    expect($rest)->toBe(['extra' => 'Extra']);
  });

  it('should throw an error when REST property is missing', function () {
    expect(fn() => new HeaderPropsWithMissingRestProperty(['title' => 'Title', 'extra' => 'Extra']))->toThrow('missing public property `$__` to store extra properties');
  });

  it('should allow destructuring', function () {
    $props = new HeaderProps(['title' => 'Title', 'subtitle' => 'Subtitle']);
    ['title' => $title, 'subtitle' => $subtitle] = $props;

    expect($title)->toBe('Title');
    expect($subtitle)->toBe('Subtitle');
  });

  it('should allow array access', function () {
    $props = new HeaderProps(['title' => 'Title', 'subtitle' => 'Subtitle']);

    expect($props['title'])->toBe('Title');
    expect($props['subtitle'])->toBe('Subtitle');
  });

  it('should allow property access', function () {
    $props = new HeaderProps(['title' => 'Title', 'subtitle' => 'Subtitle']);

    expect($props->title)->toBe('Title');
    expect($props->subtitle)->toBe('Subtitle');
  });

  it('should allow destruct REST properties', function () {
    $props = new HeaderPropsWithRest(['title' => 'Title', 'subtitle' => 'Subtitle', 'extra' => 'Extra']);
    ['title' => $title, 'subtitle' => $subtitle, '__' => $rest] = $props;

    expect($title)->toBe('Title');
    expect($subtitle)->toBe('Subtitle');
    expect($rest)->toBe(['extra' => 'Extra']);
  });
});
