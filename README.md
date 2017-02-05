# Database logic organisation for PHP 7 projects.

Encloses your application's database scripts within a simple and standardised interface, separating database access from your page logic.

Documentation: https://php.gt/docs/database

***

<a href="https://circleci.com/gh/PhpGt/Database" target="_blank">
	<img src="https://img.shields.io/circleci/project/PhpGt/Database/master.svg?style=flat-square" alt="Build status" />
</a>
<a href="https://scrutinizer-ci.com/g/PhpGt/Database" target="_blank">
	<img src="https://img.shields.io/scrutinizer/g/PhpGt/Database/master.svg?style=flat-square" alt="Code quality" />
</a>
<a href="https://scrutinizer-ci.com/g/PhpGt/Database" target="_blank">
	<img src="https://img.shields.io/scrutinizer/coverage/g/PhpGt/Database/master.svg?style=flat-square" alt="Code coverage" />
</a>
<a href="https://packagist.org/packages/PhpGt/Database" target="_blank">
	<img src="https://img.shields.io/packagist/v/PhpGt/Database.svg?style=flat-square" alt="Current version" />
</a>

## Example usage: CRUD operations

This library provides database access via raw SQL or using a PHP Query Builder, both interoperable through the same API, shown below:

```php
// "Retrieve" or "get" methods always return a ResultSet.
$shopItems = $db["shop"]->getItemsInCategory("books");

foreach($shopItems as $item) {
	setItemName($item["name"]);
	setItemPrice($item["price"]);
}

// The fields of the first (or only) Row of a ResultSet can be addressed on the
// ResultSet itself:
$customer = $db["customer"]->getById(105);
outputGreeting("Hello, " . $customer["first_name"]);

// "Create" or "insert" methods always return the last inserted ID:
$newCustomerId = $db["customer"]->create([
	"first_name" => "Marissa",
	"last_name" => "Mayer",
	"dob" => new DateTime("1975-05-30"),
]);

// "Update" or "set" methods, as well as "delete" or "remove" methods
// always return the number of affected rows:
$numberOfItemsAffected = $db["item"]->updatePrice([
	"percent" => 12.5
	"max_increase" => 20.00
]);

$numberOfDeletedReviews = $db["review"]->deleteOldReviews([
	"createdAfter" => new DateTime("-6 months"),
]);
```

## Features at a glance

+ [Organisation of queries using `QueryCollection`s][wiki-query-collections]
+ [Bind parameters by name or sequentially][wiki-parameters]
+ [Automatic database migrations][wiki-migrations]
+ [Interoperable SQL and PHP Query Builder][wiki-sql-php] (future release)
+ [Fully configurable][wiki-config]

## Compatible database engines

Compatibility is provided for the following database providers:

* MySQL
* Postgres
* SQLite
* SQL Server
* Mongo (planned)
* CouchDB (planned)