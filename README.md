[![Latest Stable Version](https://poser.pugx.org/freshbitsweb/laratables/v/stable)](https://packagist.org/packages/freshbitsweb/laratables)
[![Total Downloads](https://poser.pugx.org/freshbitsweb/laratables/downloads)](https://packagist.org/packages/freshbitsweb/laratables)
[![License](https://poser.pugx.org/freshbitsweb/laratables/license)](https://packagist.org/packages/freshbitsweb/laratables)
[![StyleCI](https://styleci.io/repos/108421119/shield?branch=master)](https://styleci.io/repos/108421119)

# Laratables (Laravel 5.5+)
A Laravel package to handle server side ajax of [Datatables](https://datatables.net).

## Table of contents
* [Introduction](#introduction)
* [How to use](#how-to-use)
    * [Client side](#client-side)
    * [Server side](#server-side)
* [Installation](#installation)
* [Customization](#customization)
    * [Custom columns](#custom-columns)

## Introduction
This package helps with simple requirements of displaying data from eloquent models into datatables with ajax support. Plus, using simple relationships and customizing column values.

## How to use
The basic approach is that you can specify the Datatable configuration and columns on the client side just like you would without any major change and call a single method on the server side to handle ajax calls. The package will create necessary queries to fetch the data automatically. If needed, You can step-in and customize query/data/logic at various stages by adding methods in your Eloquent model.

### Client side
```
$('#users-table').DataTable({
    serverSide: true,
    ajax: "{{ route('admin.users.datatables') }}",
    columns: [
        { name: 'id' },
        { name: 'name' },
        { name: 'email' },
        { name: 'team.url' },
        { name: 'action', orderable: false, searchable: false }
    ],
    ...
});
```

### Server side
```
use App\User;
use Freshbitsweb\Laratables\Laratables;
...
return Laratables::recordsOf(User::class);
```

Check the Customization section below to see how you can customize query/data/logic, etc.

## Installation
Install the package by running this command in your terminal/cmd:
```
composer require freshbitsweb/laratables
```

Optionally, you can import config file by running this command in your terminal/cmd:
```
php artisan vendor:publish --tag=laratables_config
```

## Customization
Following the steps of How to use section should get you up and running with a simple datatables example in a minute. However, many datatables require customization ability. Here are the the options:

### Custom columns
Generally, we need one or more columns that are not present in the database table. The most common example is 'Action' column to provide options to edit or delete the record. You can add a static method `laratablesCustom[ColumnName]()` to your model file to specify the custom column value. As per our example, it could be:

```php
/**
 * Returns the action column html for datatables.
 *
 * @param \App\User
 * @return string
 */
public static function laratablesCustomAction($user)
{
    return view('admin.users.includes.index_action', compact('user'))->render();
}
```
As you may have observed, you receive an eloquent object of the record as a parameter to use the record details in your method.

### Relationship columns
We also need to display data from related models, right? And it's super easy here. No need to do anything on server side for simple relationships. Just specify the name of the relation and the name of the column on the client side inside columns array.

```js
columns: [
    ...
    { name: 'team.url' },
    ...
],
```

### Customizing column values
Sometimes, you may need to customize the value of a table column before displaying it in the datatables. Just add a method `laratables[ColumnName]()` in your eloquent model to play with that:

```php
/**
 * Returns truncated name for the datatables.
 *
 * @return string
 */
public function laratablesName()
{
    return str_limit($this->name, 15);
}
```
Relationship columns can also be customized by adding a method in this format `laratables[RelationName][ColumnName]()`.

These methods are called on the eloquent model object giving you full power of `$this`.

### Controlling the query
You may want to apply additional where conditions to the query or load additional relationships. `laratablesQueryConditions()` static method to the rescue.

```php
/**
 * Fetch only active users in the datatables.
 *
 * @param \Illuminate\Database\Eloquent\Builder
 * @param \Illuminate\Database\Eloquent\Builder
 */
public static function laratablesQueryConditions($query)
{
    return $query->where('active', true);
}
```
This method accepts and returns a `$query` object.

You can also control the relationship query by defining a closure which can be used while eager loading the relationship records. The static method name format is `laratables[RelationName]RelationQuery`.

```php
/**
 * Fetch only active users in the datatables.
 *
 * @return callable
 */
public static function laratablesRoleRelationQuery()
{
    return function ($query) {
        $query->with(['status:id,label']);
    };
}
```

## Authors

* [**Gaurav Makhecha**](https://github.com/gauravmak) - *Initial work*

See also the list of [contributors](https://github.com/freshbitsweb/laratables/graphs/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details

## Special Thanks to

* [Laravel](https://laravel.com) Community
