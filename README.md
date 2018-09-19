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
* [Online Demo](#online-demo)
* [Installation](#installation)
* [Customization](#customization)
    * [Custom columns](#custom-columns)
    * [Relationship columns](#relationship-columns)
    * [Customizing column values](#customizing-column-values)
    * [Controlling the query](#controlling-the-query)
    * [Searching](#searching)
    * [Ordering (Sorting)](#ordering-sorting)
    * [Selecting additional columns](#selecting-additional-columns)
    * [Date format for Carbon instances](#date-format-for-carbon-instances)
    * [Modify fetched records](#modify-fetched-records)
    * [Extra data- Datatables attributes](#extra-data--datatables-attributes)

## Introduction
This package helps with simple requirements of displaying data from eloquent models into datatables with ajax support. Plus, using simple relationships and customizing column values.

## How to use
The basic approach is that you can specify the Datatable configuration and columns on the client side just like you would without any major change and call a single method on the server side to handle ajax calls. The package will create necessary queries to fetch the data and make the search and ordering functionality work automatically. If needed, You can step-in and customize query/data/logic at various stages by adding methods in your Eloquent model.

### Client side
```js
$('#users-table').DataTable({
    serverSide: true,
    ajax: "{{ route('admin.users.datatables') }}",
    columns: [
        { name: 'id' },
        { name: 'name' },
        { name: 'email' },
        { name: 'role.name' },
        { name: 'action', orderable: false, searchable: false }
    ],
    ...
});
```

### Server side
```php
use App\User;
use Freshbitsweb\Laratables\Laratables;
...
return Laratables::recordsOf(User::class);
```
Optionally, you can pass a closure as a second parameter to refine the query:
```php
use App\User;
use Freshbitsweb\Laratables\Laratables;
...
return Laratables::recordsOf(User::class, function($query)
{
    return $query->where('active', true);
});
```

## Online Demo

The demo of the package can be found at - https://laratables.freshbits.in

Check the Customization section below to see how you can customize query/data/logic, etc.

## Installation
Install the package by running this command in your terminal/cmd:
```bash
composer require freshbitsweb/laratables
```

Optionally, you can import config file by running this command in your terminal/cmd:
```bash
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
    { name: 'role.name' },
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
 * Eager load media items of the role for displaying in the datatables.
 *
 * @return callable
 */
public static function laratablesRoleRelationQuery()
{
    return function ($query) {
        $query->with('media');
    };
}
```

### Searching
Datatables provides searching functionality to filter out results based on any of the displayed columns values. While this package automatically applies *orWhere* conditions with like operator, you can put your own conditions for any column. We provide static method `laratablesSearch[ColumnName]()` for the same.

```php
/**
 * Adds the condition for searching the name of the user in the query.
 *
 * @param \Illuminate\Database\Eloquent\Builder
 * @param string search term
 * @param \Illuminate\Database\Eloquent\Builder
 */
public static function laratablesSearchName($query, $searchValue)
{
    return $query->orWhere('first_name', 'like', '%'. $searchValue. '%')
        ->orWhere('surname', 'like', '%'. $searchValue. '%')
    ;
}
```

If any of the columns is a relationship column, the package is smart enough to apply `orWhereHas` query with necessary closure to filter out the records accordingly. And you can override that behaviour by adding `laratablesSearch[RelationName][ColumnName]()` static method to your eloquent model.

**Note** - You can add `searchable: false` to any of the columns in Datatables configuration to prevent searching operation for that column.

### Ordering (Sorting)
Ordering for regular table columns works by default. For relationship columns or custom columns, you should either add `orderable: false` to Datatables column configuration or add a static method `laratablesOrder[ColumnName]()` and return the name of table column that should be used for ordering the records instead. For example, if your table contains *first_name* and Datatables has just *name*, you can add:

```php
/**
 * first_name column should be used for sorting when name column is selected in Datatables.
 *
 * @return string
 */
public static function laratablesOrderName()
{
    return 'first_name';
}
```

### Selecting additional columns
We have coded the package in a way where the query selects only required columns from the database table. If you need values of additional column when you are customizing column values or searching in other columns, you may specify them in an array inside `laratablesAdditionalColumns()` static method. For example, if you have *first_name* and *surname* in the table and you're just using a custom column *name* instead, you can add:

```php
/**
 * Additional columns to be loaded for datatables.
 *
 * @return array
 */
public static function laratablesAdditionalColumns()
{
    return ['first_name', 'surname'];
}
```

### Date format for Carbon instances
By default, Laravel treats *created_at* and *updated_at* as Carbon instances and you can also treat any other column of your table as a Carbon instance as well. This package has a config option `date_format` to specify the format in which the dates should be returned for Datatables. Default format is 'Y-m-d H:i:s'.

### Modify fetched records
Sometimes, we need to work with the records after they are already fetched. You can add `laratablesModifyCollection()` static method to your model to play with the collection and return the updated one. Note that if you add/remove any items from the collection Datatables count will have a mismatch.

```php
/**
 * Set user full name on the collection.
 *
 * @param \Illuminate\Support\Collection
 * @param \Illuminate\Support\Collection
 */
public static function laratablesModifyCollection($users)
{
    return $users->map(function ($user) {
        $user->full_name = $user->first_name . ' '. $user->last_name;

        return $user;
    });
}
```

### Extra `data-` Datatables attributes
Datatables [accepts](https://datatables.net/manual/server-side#Returned-data) extra *data-* attributes with each of the record. Following are supported with the package:

1. **DT_RowId**: This data attribute is added to each of the record by default. You can update `row_id_prefix` config option to set the prefix to that record id.

2. **DT_RowClass**: You can add `laratablesRowClass()` method to your model and return a class name. Example:

```php
/**
 * Specify row class name for datatables.
 *
 * @return string
 */
public function laratablesRowClass()
{
    return $this->is_active ? 'text-success' : 'text-warning';
}
```

3. **DT_RowData**: You can add `laratablesRowData()` method to your model and return an array with key as the attribute name and its corresponding value. Example:

```php
/**
 * Returns the data attribute for url to the edit page of the user.
 *
 * @return array
 */
public function laratablesRowData()
{
    return [
        'edit-url' => route('admin.user.edit', ['user' => $this->id]),
    ];
}
```

## Authors

* [**Gaurav Makhecha**](https://github.com/gauravmak) - *Initial work*

See also the list of [contributors](https://github.com/freshbitsweb/laratables/graphs/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details

## Special Thanks to

* [Laravel](https://laravel.com) Community
