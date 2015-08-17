# Support
[![Author](https://img.shields.io/badge/author-%40ianmolson-blue.svg)](https://twitter.com/ianmolson)
[![Github Release](https://img.shields.io/github/release/iolson/support.svg)](https://github.com/iolson/support)
[![Packagist](https://img.shields.io/packagist/l/iolson/support.svg)](https://packagist.org/packages/iolson/support)
[![Packagist](https://img.shields.io/packagist/dt/iolson/support.svg)](https://packagist.org/packages/iolson/support)

This is a Laravel based package that I have created for code to be easily reused for tedious tasks in creating an application.

## Installation

```bash
$ composer require iolson/support
```

And then include the service provider within your `app/config/app.php`.

```php
'providers' => [
	IanOlson\Support\Providers\SupportServiceProvider::class,
];
```

If you would like to have Meta Data for your models, be sure to publish the vendor files:

```bash
$ php artisan vendor:publish
```

## Usage

Coming at some point when I have time to write them up.

### ValidateTrait

Add the trait as a use entry on your repository. This will pull in the Laravel validation method. Before you call the validation method, you will setup the rules that will need to validate the request by using the following. `$data` is a passed in data of `$request->all()` from a controller.

```php
/**
 * {@inheritDoc}
 */
public function create(array $data)
{
    // Setup validation rules.
    $this->rules = [
        //
    ];

    // Run validation.
    $this->validate($data);
    
    ...
}
```

## Support

The following support channels can be used for contact.

- [Twitter](https://twitter.com/ianmolson)
- [Email](mailto:me@ianolson.io)

Bug reports, feature requests, and pull requests can be submitted following our [Contribution Guide](CONTRIBUTING.md).

## Contributing & Protocols

- [Versioning](CONTRIBUTING.md#versioning)
- [Coding Standards](CONTRIBUTING.md#coding-standards)
- [Pull Requests](CONTRIBUTING.md#pull-requests)

## Roadmap

Will be adding anything that I keep using on a consistent basis to this package so I'm not rewriting things all the time.

## License

This software is released under the [MIT](LICENSE.md) License.

&copy; 2015 Ian Olson, All rights reserved.