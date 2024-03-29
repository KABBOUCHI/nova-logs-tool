# Nova tool to for logs

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kabbouchi/nova-logs-tool.svg?style=flat-square)](https://packagist.org/packages/kabbouchi/nova-logs-tool)
[![Total Downloads](https://img.shields.io/packagist/dt/kabbouchi/nova-logs-tool.svg?style=flat-square)](https://packagist.org/packages/kabbouchi/nova-logs-tool)

A Laravel Nova tool to manage and keep track of each one of your logs files.

![screenshot of the backup tool](https://raw.githubusercontent.com/KABBOUCHI/nova-logs-tool/master/docs/screenshot.png?20180828)

Behind the scenes [kabbouchi/laravel-ward](https://github.com/KABBOUCHI/laravel-ward) is used.
> You can disable `laravel-ward` routes by adding `LOG_VIEWER_ENABLE_ROUTES=false` to `.env` file

## Installation

You can install the package in to a Laravel app that uses [Nova](https://nova.laravel.com) via composer:

```bash
composer require kabbouchi/nova-logs-tool

php artisan vendor:publish --tag=ward-assets --force
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

Publish the package configuration file.

```bash
php artisan vendor:publish --provider="KABBOUCHI\LogsTool\LogsToolServiceProvider"
```

## Authorization
```php
// in app/Providers/NovaServiceProvder.php

// ...

public function tools()
{
    return [
        // ...
        // don't return plain `true` value or anyone can see/download/delete the logs, make sure to check if user has permission.
        (new \KABBOUCHI\LogsTool\LogsTool())
                ->canSee(function ($request) {
                    return auth()->user()->canSee(); 
                })
                ->canDownload(function ($request) {
                    return  auth()->user()->canDownload();
                })
                ->canDelete(function ($request) {
                    return false;
                }),
    ];
}
```

## Usage

Click on the "nova-logs-tool" menu item in your Nova app to see the tool provided by this package.

Possible environment variables:

``` env
NOVA_LOGS_PER_PAGE=6
NOVA_LOGS_REGEX_FOR_FILES="/^laravel/"
```

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
