# WP Endpoint Authors

> Query against a WordPress site and get a JSON response with a
> collection of authors associated with your request.

## Getting Started

The easiest way to install this package is by using composer from your terminal:

```bash
composer require moxie-lean/wp-endpoints-authors
```

Or by adding the following lines on your `composer.json` file

```json
"require": {
  "moxie-lean/wp-endpoints-authors": "dev-master"
}
```

This will download the files from the [packagist site](https://packagist.org/packages/moxie-lean/wp-endpoints-authors)
and set you up with the latest version located on master branch of the repository.

After that you can include the `autoload.php` file in order to
be able to autoload the class during the object creation.

```php
include '/vendor/autoload.php';

\Lean\Endpoints\Authors::init();
```

## Features

Returns only a collection of authors.

## Usage.

The default URL is:

````
/wp-json/leean/v1/authors
```

By default the collection is the list of authors, you can use most
of the WP_User_Query params in order to update your results.

## Request examples

Get list of authors.

```json
wp-json/leean/v1/authors-collection?role=author
```
