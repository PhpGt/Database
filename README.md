<img src="logo.png" alt="Database logic organisation for PHP 7 projects." align="right" />

# Database API organisation for PHP 7 projects.

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

## Example usage

This library organises SQL access through a consistent API. To execute an example query located at `src/query/shop_item/getItemsInCategory.sql`, the following pattern is used:

```php
$user = $db->fetch("user/getById", 105);
```

Examples of CRUD operations:

```php
// "fetchAll" method returns an array of Row objects, optionally constructed as 
// a custom type with well-typed properties and helper methods.

$bookList = $db->fetchAll("shopitem/getItemsInCategory", "books");

foreach($bookList as $book) {
	echo "Book title: " . $book->title . PHP_EOL;
	echo "Book price: " . $book->price . PHP_EOL;
	
	if($book->offerEnds) {
		echo "Item on offer until: " . $book->offerEnds->format("dS M Y");
	}
}

// "Create" method always returns the last inserted ID:
$newCustomerId = $db->create("customer/new", [
	"first_name" => "Marissa",
	"last_name" => "Mayer",
	"dob" => new DateTime("1975-05-30"),
]);

// "Update" or "delete" methods always return the number of affected rows:
$numberOfItemsAffected = $db->update("shop/item/increasePrice", [
	"percent" => 12.5
	"max_increase" => 20.00
]);

$numberOfDeletedReviews = $db->delete(
	"remove/deleteOlderThan",
	new DateTime("-6 months")
);
```

## Features at a glance

+ [SQL templates][wiki-templates] (future release)
+ [Automatic database migrations][wiki-migrations]
+ [Organisation of queries using `QueryCollection`s][wiki-query-collections]
+ [Bind parameters by name or sequentially][wiki-parameters]
+ [Fully configurable][wiki-config]

## Compatible database engines

Compatibility is provided for the following database providers:

* MySQL
* Postgres
* SQLite
* SQL Server
* Mongo (planned)
* CouchDB (planned)

[wiki-templates]: https://github.com/PhpGt/Database/wiki
[wiki-query-collections]: https://github.com/PhpGt/Database/wiki
[wiki-parameters]: https://github.com/PhpGt/Database/wiki
[wiki-migrations]: https://github.com/PhpGt/Database/wiki
[wiki-config]: https://github.com/PhpGt/Database/wiki