<p align="center">
<a href="https://packagist.org/packages/probonolabs/laravel-api-generator"><img src="https://img.shields.io/packagist/dt/probonolabs/laravel-api-generator" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/probonolabs/laravel-api-generator"><img src="https://img.shields.io/packagist/v/probonolabs/laravel-api-generator" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/probonolabs/laravel-api-generator"><img src="https://img.shields.io/packagist/l/probonolabs/laravel-api-generator" alt="License"></a>
</p>

This package contains one single command: `php artisan make:api {name}` which creates a complete CRUD resource. 

Each CRUD resource contains:
- Single Action Controller: Index, Get, Create, Update and Delete
- Custom request for each controller
- Custom resource
- Model and migration
- Routes

Publish this package config if you want to change the base controller, request, resource or add default route middleware.


## Installation

You can install the package via composer:

```bash
composer require probonolabs/laravel-api-generator --dev
```

## Usage

You can create an API resource by using this command:

```bash
php artisan make:api Student
```

Create a nested API resource by using this command:
```bash
php artisan make:api Student/Training
```

## License

The MIT License (MIT). Please see [License File](license.md) for more information.
