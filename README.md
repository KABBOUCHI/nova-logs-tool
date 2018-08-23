# [WIP] Nova tool to for logs

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kabbouchi/nova-logs-tool.svg?style=flat-square)](https://packagist.org/packages/kabbouchi/nova-logs-tool)
[![Total Downloads](https://img.shields.io/packagist/dt/kabbouchi/nova-logs-tool.svg?style=flat-square)](https://packagist.org/packages/kabbouchi/nova-logs-tool)

This Nova tool for logs

Behind the scenes [kabbouchi/laravel-ward](https://github.com/KABBOUCHI/laravel-ward) is used.

## Installation

You can install the package in to a Laravel app that uses [Nova](https://nova.laravel.com) via composer:

```bash
composer require kabbouchi/nova-logs-tool
```

Next up, you must register the tool with Nova. This is typically done in the `tools` method of the `NovaServiceProvider`.

```php
// in app/Providers/NovaServiceProvder.php

// ...

public function tools()
{
    return [
        // ...
        new \KABBOUCHI\LogsTool\LogsTool(),
    ];
}
```

## Usage

Click on the "nova-logs-tool" menu item in your Nova app to see the tool provided by this package.

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Georges KABBOUCHI](https://github.com/kabbouchi)

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
