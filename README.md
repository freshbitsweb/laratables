[![Latest Stable Version](https://poser.pugx.org/freshbitsweb/laratables/v/stable)](https://packagist.org/packages/freshbitsweb/laratables)
[![Total Downloads](https://poser.pugx.org/freshbitsweb/laratables/downloads)](https://packagist.org/packages/freshbitsweb/laratables)
[![License](https://poser.pugx.org/freshbitsweb/laratables/license)](https://packagist.org/packages/freshbitsweb/laratables)
[![StyleCI](https://styleci.io/repos/108421119/shield?branch=master)](https://styleci.io/repos/108421119)
[![Buy us a tree](https://img.shields.io/badge/Buy%20me%20a%20tree-%F0%9F%8C%B3-lightgreen?style=flat-square)](https://plant.treeware.earth/freshbitsweb/laratables)


# Package Status
This package is not under active development. We no longer plan to add new features.
The main reason being that our company projects do not use Datatables anymore.

If you wish to maintain a fork of this package, I would be more than happy to link.


# Laratables (Laravel 5.5 to Laravel 9.x)
A Laravel package to handle server side ajax of [Datatables](https://datatables.net).

## Table of contents
* [Introduction](#introduction)
* [How to use](#how-to-use)
    * [Client side](#client-side)
    * [Server side](#server-side)
* [Demo Repositories](#demo-repositories)
* [Installation](#installation)
* [Customization](#customization)
    * [Controlling the query](#controlling-the-query)
        * [Controlling the relationship query](#controlling-the-relationship-query)
        * [Joining related tables to the query](#joining-related-tables-to-the-query)
    * [Custom columns](#custom-columns)
    * [Relationship columns](#relationship-columns)
    * [Customizing column values](#customizing-column-values)
    * [Searching](#searching)
    * [Ordering (Sorting)](#ordering-sorting)
    * [Selecting additional columns](#selecting-additional-columns)
    * [Specifying additional searchable columns](#specifying-additional-searchable-columns)
    * [Date format for Carbon instances](#date-format-for-carbon-instances)
    * [Modify fetched records](#modify-fetched-records)
    * [Extra data- Datatables attributes](#extra-data--datatables-attributes)

## Introduction
This package helps with simple requirements of displaying data from eloquent models into datatables with ajax support. Plus, using simple relationships and customizing column values. Laratables does not work with Datatables Editor yet.

## How to use
The basic approach is that you can specify the Datatable configuration and columns on the client side just like you would without any major change and call a single method on the server side to handle ajax calls. The package will create necessary queries to fetch the data and make the search and ordering functionality work automatically. If needed, You can step-in and customize query/data/logic at various stages by adding methods in your Eloquent model or a custom class.

### Client side
```js
$('#users-table').DataTable({
    serverSide: true,
    ajax: "{{ route('admin.users.datatables') }}",
    columns: [
        { name: 'id' },
        { name: 'name' },
        { name: 'email' },
        { name: 'role.name', orderable: false },
        { name: 'action', orderable: false, searchable: false }
    ],
    ...
});
```

⚠️ **IMP Note** ⚠️ - The client side code decides the columns that should be fetched. If you are showing the table on a public facing page, malicious users can modify the HTTP request to fetch data for other columns of respective table and related tables. It is highly recommend that you validate the requests before returning the data.


### Server side
```php
use App\User;
use Freshbitsweb\Laratables\Laratables;
...
return Laratables::recordsOf(User::class);
```

There are multiple ways to customize query/data/logic. Check [Customization](#customization) section below for details.

**[⬆ back to top](#table-of-contents)**

## Demo Repositories

1. https://github.com/freshbitsweb/laratables-demo-one-to-many
2. https://github.com/freshbitsweb/laratables-demo-customize-column
3. https://github.com/freshbitsweb/laratables-demo-basic
4. https://github.com/freshbitsweb/laratables-demo-many-to-many
5. https://github.com/freshbitsweb/laratables-demo-one-to-many-polymorphic
6. https://github.com/freshbitsweb/laratables-demo-one-to-one


## Requirements
| PHP    | Laravel | Package |
|--------|---------|---------|
| 8.0+   | 9.x     | v2.5.0  |
| 7.3+   | 8.x     | v2.4.0  |
| 7.2.5+ | 7.x     | v2.3.0  |
| <7.2.5 | 6.x     | v2.2.0  |
| 7.1    | 5.x     | v2.0.*  |

## Installation
Install the package by running this command in your terminal/cmd:
```bash
composer require freshbitsweb/laratables
```

Optionally, you can import config file by running this command in your terminal/cmd:
```bash
php artisan vendor:publish --tag=laratables_config
```

**[⬆ back to top](#table-of-contents)**

## Customization
Following the steps of How to use section should get you up and running with a simple datatables example in a minute. However, many datatables require customization ability. First use case is about applying additional where conditions to the query or load additional relationships.

To achieve that, you can simply pass a closure/callable as a second parameter to `recordsOf()` method. It should accept the underlying query as a parameter and return it after applying conditionals:
```php
use App\User;
use Freshbitsweb\Laratables\Laratables;
...
return Laratables::recordsOf(User::class, function($query)
{
    return $query->where('active', true);
});
```

There are many more options to customize query/data/logic. You can add any of the following methods (they start with `laratables` word) in your **model or a custom class** to keep your model neat and clean. Specify the name of the custom class as the second argument to the `recordsOf()` method if you wish to use one:

```php
use App\User;
use Freshbitsweb\Laratables\Laratables;
use App\Laratables\User as UserLaratables;
...
return Laratables::recordsOf(User::class, UserLaratables::class);
```

**[⬆ back to top](#table-of-contents)**

### Controlling the query

If you wish to apply conditions everytime a model is used to display a Laratable, add `laratablesQueryConditions()` static method to the model/custom class.

```php
/**
 * Fetch only active users in the datatables.
 *
 * @param \Illuminate\Database\Eloquent\Builder
 * @return \Illuminate\Database\Eloquent\Builder
 */
public static function laratablesQueryConditions($query)
{
    return $query->where('active', true);
}
```
This method accepts and returns a `$query` object.

#### Controlling the relationship query

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

#### Joining related tables to the query

The `laratablesQueryConditions()` method can also be used to add joins on the base table.  This is particularly useful if you need to define custom searching and sorting based on related models, for example:

```php
/**
 * Join roles to base users table.
 * Assumes roles -> users is a one-to-many relationship
 *
 * @param \Illuminate\Database\Eloquent\Builder
 * @return \Illuminate\Database\Eloquent\Builder
 */
public static function laratablesQueryConditions($query)
{
    return $query->join('roles', 'roles.id', 'users.role_id');
}
```

***Note*** - If searching/filtering by columns from the joined table, you will need to also ensure they are included in the selected columns.  A couple of options for how to do this include:

* Chaining a `->select()` method above - e.g. `$query->join(...)->select(['roles.name as role_name'])`, or
* Using the `laratablesAdditionalColumns()` method as described in [Selecting additional columns](#selecting-additional-columns) below.

**[⬆ back to top](#table-of-contents)**

### Custom columns
Generally, we need one or more columns that are not present in the database table. The most common example is 'Action' column to provide options to edit or delete the record. You can add a static method `laratablesCustom[ColumnName]()` to your model/custom class to specify the custom column value. As per our example, it could be:

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

**[⬆ back to top](#table-of-contents)**

### Relationship columns
We also need to display data from related models, right? And it's super easy here. No need to do anything on server side for simple relationships. Just specify the name of the relation and the name of the column on the client side inside columns array.

```js
columns: [
    ...
    { name: 'role.name', orderable: false },
    ...
],
```

Ordering records by a relationship column is not supported in Laravel as main table records are fetched first and another query is fired to fetch related table records. Therefore, you should always keep your relationship table columns to be `orderable: false`.

**Note** - The package does not support [nested relationships](https://github.com/freshbitsweb/laratables/issues/6) yet. You can utilize the custom column feature to get the nested relationship data but make sure that you [eager load](https://github.com/freshbitsweb/laratables#controlling-the-query) the relationship records.

**[⬆ back to top](#table-of-contents)**

### Customizing column values
Sometimes, you may need to customize the value of a table column before displaying it in the datatables. Just add a static method `laratables[ColumnName]()` in your eloquent model/custom class to play with that:

```php
/**
 * Returns truncated name for the datatables.
 *
 * @param \App\User
 * @return string
 */
public static function laratablesName($user)
{
    return Str::limit($user->name, 15);
}
```
Relationship columns can also be customized by adding a static method in this format `laratables[RelationName][ColumnName]()`.

These static methods are called on the eloquent model/custom class with the eloquent object of the record as a parameter.

**Note** - These methods were regular methods in v1.\*.\*

**[⬆ back to top](#table-of-contents)**

### Searching
Datatables provides searching functionality to filter out results based on any of the displayed columns values. While this package automatically applies *orWhere* conditions with like operator, you can put your own conditions for any column. We provide static method `laratablesSearch[ColumnName]()` for the same.

```php
/**
 * Adds the condition for searching the name of the user in the query.
 *
 * @param \Illuminate\Database\Eloquent\Builder
 * @param string search term
 * @return \Illuminate\Database\Eloquent\Builder
 */
public static function laratablesSearchName($query, $searchValue)
{
    return $query->orWhere('first_name', 'like', '%'. $searchValue. '%')
        ->orWhere('surname', 'like', '%'. $searchValue. '%')
    ;
}
```

If any of the columns is a relationship column, the package is smart enough to apply `orWhereHas` query with necessary closure to filter out the records accordingly. And you can override that behaviour by adding `laratablesSearch[RelationName][ColumnName]()` static method to your eloquent model/custom class.

**Note** - You can add `searchable: false` to any of the columns in Datatables configuration (client side) to prevent searching operation for that column. Or you can also add the name of the column to the `non_searchable_columns` array in the config file.

**[⬆ back to top](#table-of-contents)**

### Ordering (Sorting)
Ordering for regular table columns works by default. Multi-column sorting is also supported. For relationship columns or custom columns, you should either add `orderable: false` to Datatables column configuration or add a static method `laratablesOrder[ColumnName]()` and return the name of main table column that should be used for ordering the records instead. For example, if your table contains *first_name* and Datatables has just *name*, you can add:

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

You can also order rows by a raw statement by adding a static method `laratablesOrderRaw[ColumnName]()` and return the raw statement that we would put in *orderByRaw()* of the query. The function receives the `$direction` variable which will be either *asc* or *desc*.
```php
/**
 * first_name and last_name columns should be used for sorting when name column is selected in Datatables.
 *
 * @param string Direction
 * @return string
 */
public static function laratablesOrderRawName($direction)
{
    return 'first_name '.$direction.', last_name '.$direction;
}
```

**[⬆ back to top](#table-of-contents)**

### Selecting additional columns
We have coded the package in a way where the query selects only required columns from the database table. If you need values of additional column when you are customizing column values, you may specify them in an array inside `laratablesAdditionalColumns()` static method. For example, if you have *first_name* and *surname* in the table and you're just using a custom column *name* instead, you can add:

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

**[⬆ back to top](#table-of-contents)**

### Specifying additional searchable columns
If you need to search for values in columns of the table which aren't displayed in the Datatables, you may specify them in an array inside `laratablesSearchableColumns()` static method. For example, if you have *first_name* and *surname* in the table and you want users to be able to search by those columns even if they are not displayed in the Datatables, you can add:

```php
/**
 * Additional searchable columns to be used for datatables.
 *
 * @return array
 */
public static function laratablesSearchableColumns()
{
    return ['first_name', 'surname'];
}
```

**[⬆ back to top](#table-of-contents)**

### Date format for Carbon instances
By default, Laravel treats *created_at* and *updated_at* as Carbon instances and you can also treat any other column of your table as a Carbon instance as well. This package has a config option `date_format` to specify the format in which the dates should be returned for Datatables. Default format is 'Y-m-d H:i:s'.

### Modify fetched records
Sometimes, we need to work with the records after they are already fetched. You can add `laratablesModifyCollection()` static method to your model/custom class to play with the collection and return the updated one. Note that if you add/remove any items from the collection, the Datatables count will have a mismatch.

```php
/**
 * Set user full name on the collection.
 *
 * @param \Illuminate\Support\Collection
 * @return \Illuminate\Support\Collection
 */
public static function laratablesModifyCollection($users)
{
    return $users->map(function ($user) {
        $user->full_name = $user->first_name . ' '. $user->last_name;

        return $user;
    });
}
```

**[⬆ back to top](#table-of-contents)**

### Extra `data-` Datatables attributes
Datatables [accepts](https://datatables.net/manual/server-side#Returned-data) extra *data-* attributes with each of the record. Following are supported with the package:

1. **DT_RowId**: This data attribute is added to each of the record by default. You can update `row_id_prefix` config option to set the prefix to that record id.

2. **DT_RowClass**: You can add `laratablesRowClass()` static method to your model/custom class and return an html class name. Example:

```php
/**
 * Specify row class name for datatables.
 *
 * @param \App\User
 * @return string
 */
public static function laratablesRowClass($user)
{
    return $user->is_active ? 'text-success' : 'text-warning';
}
```

**Note** - This method was a regular method in v1.\*.\*

3. **DT_RowData**: You can add `laratablesRowData()` static method to your model/custom class and return an array with key as the attribute name and its corresponding value. Example:

```php
/**
 * Returns the data attribute for url to the edit page of the user.
 *
 * @param \App\User
 * @return array
 */
public static function laratablesRowData($user)
{
    return [
        'edit-url' => route('admin.user.edit', ['user' => $user->id]),
    ];
}
```

**Note** - This method was a regular method in v1.\*.\*

## Authors

* [**Gaurav Makhecha**](https://github.com/gauravmak) - *Initial work*

See also the list of [contributors](https://github.com/freshbitsweb/laratables/graphs/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details

## Treeware

You're free to use this package, but if it makes it to your production environment I would highly appreciate you buying the world a tree.

It’s now common knowledge that one of the best tools to tackle the climate crisis and keep our temperatures from rising above 1.5C is to <a href="https://www.bbc.co.uk/news/science-environment-48870920">plant trees</a>. If you contribute to our forest you’ll be creating employment for local families and restoring wildlife habitats.

You can buy trees at for our forest here [offset.earth/treeware](https://plant.treeware.earth/freshbitsweb/laratables)

Read more about Treeware at [treeware.earth](http://treeware.earth)

## Special Thanks to

* [Laravel](https://laravel.com) Community
