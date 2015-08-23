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
],
```

If you would like to have Meta Data for your models, be sure to publish the vendor files:

```bash
$ php artisan vendor:publish
```

## Usage

Here is basic usage of Traits that are included in this package.

### SeoTrait

--

This trait uses [SEOTools Package](https://github.com/artesaos/seotools), which will need to be setup if you want to use this trait inside your project. You can see install instructions below.

Documentation coming soon.

### UpdateTrait

--

Add the trait as a use entry on your repository. This will pull in some new methods in order to create/update records.

```php
/**
 * Update model attributes from a request.
 *
 * @param       $model
 * @param array $data
 *
 * @return bool
 */
protected function updateAttributes(&$model, array &$data)
{
    if (empty($data)) {
        return false;
    }

    // Get mass assignment columns of the model.
    $massAssign = $model->getFillable();

    foreach ($data as $attribute => $value) {

        if (!in_array($attribute, $massAssign)) {
            continue;
        }

        $model->$attribute = $value;
    }

    return true;
}
```

```php
/**
 * Purge the cache of an array of cache keys.
 *
 * @param null $model
 * @param null $prefix
 */
protected function purgeCache($model = null, $prefix = null)
{
    if ($model && $prefix) {

        // Add $prefix.id to cache keys.
        $this->cacheKeys[] = $prefix . $model->id;

        // Get mass assignment columns of the model.
        $massAssign = $model->getFillable();

        // If slug as mass assignable add $prefix.slug to cache keys.
        if(in_array('slug', $massAssign)) {
            $this->cacheKeys[] = $prefix . $model->slug;
        }
    }

    // Clear cache keys.
    foreach ($this->cacheKeys as $key) {
        Cache::forget($key);
    }
}
```

### UploadTrait

--

Documentation coming soon.

### ValidateTrait

--

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

## Third-Party Package Usage

This package pulls in other packages, here is a quick how-to guide taken from each of the respective GitHub pages and also a link to their packages for further information on usage.

### Laravel Debugbar ([GitHub](https://github.com/barryvdh/laravel-debugbar))

--

Include the service provider within your `app/config/app.php`.

```php
'providers' => [
	Barryvdh\Debugbar\ServiceProvider::class,,
],
```

### Intervention Image ([GitHub](https://github.com/Intervention/image))

--

Include the service provider within your `app/config/app.php`.

```php
'providers' => [
	Intervention\Image\ImageServiceProvider::class,,
],
```

Include the facade within your `app/config/app.php`.

```php
'aliases' = [
	'Image' => Intervention\Image\Facades\Image::class,
],
```

### Searchable ([GitHub](https://github.com/nicolaslopezj/searchable))

--

Add the trait to your model and your search rules.

```php
use Nicolaslopezj\Searchable\SearchableTrait;

class User extends \Eloquent
{
    use SearchableTrait;

    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable = [
        'columns' => [
            'first_name' => 10,
            'last_name' => 10,
            'bio' => 2,
            'email' => 5,
            'posts.title' => 2,
            'posts.body' => 1,
        ],
        'joins' => [
            'posts' => ['users.id','posts.user_id'],
        ],
    ];

    public function posts()
    {
        return $this->hasMany('Post');
    }

}
```

Now you can search your model.

```php
// Simple search
$users = User::search($query)->get();

// Search and get relations
// It will not get the relations if you don't do this
$users = User::search($query)
            ->with('posts')
            ->get();
```

### SEOTools ([GitHub](https://github.com/artesaos/seotools) | [English Readme](https://github.com/artesaos/seotools/blob/master/README-en_US.md))

--

Include the service provider within your `app/config/app.php`.

```php
'providers' => [
	Artesaos\SEOTools\Providers\SEOToolsServiceProvider::class,,
],
```

Include the facade within your `app/config/app.php`.

```php
'aliases' = [
	'SEO' => Artesaos\SEOTools\Facades\SEOTools::class,
],
```

```bash
$ php artisan vendor:publish
```

You will need to edit the default config that gets published which will be located at `app/config/seotools.php`. Under the defaults there is a default SEO tags that will get appended to the ones that you setup.

Add the following in your views file `{!! SEO::generate() !!}`. Here is an example.

```php
<html>
	<head>
		{!! SEO::generate() !!}
	</head>
	<body>
	</body>
</html>
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