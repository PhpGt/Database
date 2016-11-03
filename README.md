# Separates database logic through a simple interface.

***

// TODO: Add Shields.io badges.

## Usage example.

```php
use \Gt\Database\Connection\Settings;
use \Gt\Database\Client;

// Create a database connection:
$settings = new Settings(
	Settings::DRIVER_MYSQL,
	"MyDatabaseName",
	"localhost",
	"db_username",
	"p4ssw0rd"
);

$db = new Client($settings);

// Prepare statement.
// Query is physically located at src/query/blog/retrieveCommentsForBlog.sql
// Named placeholders are within SQL file.
$query = $db["blog"]->query("retrieveCommentsForBlog", [
	"id_blog" => 123,
]);

// Iterate over all rows.
foreach($query->fetchAll() as $comment) {
	// ...
}

```

## Method outline:

+ `Client::tableCollection(string $name):TableCollectionInterface` synonym for `ArrayAccess::offsetGet`. Gets `TableCollection` according to name.
+ `Client::table(string $name):Illuminate\Database\Query\Builder` access the raw database table by name, bypassing TableCollections.

+ `TableCollection::query(string $name, array $variables)` return a `Query` object ready for further filtering or execution.

+ `Query::fetch():ResultRow` execute the query, returning one row (the first, or next) of the result set.
+ `Query::fetchAll()::ResultCollection` execute the query, returning all rows in the result set.

Compatibility is provided for the following database providers:

* MySQL.
* Postgres.
* SQLite.
* SQL Server.
* Mongo (planned).
* CouchDB (planned).

Contains your application's database scripts within a simple and standardised interface, separating database access from your page logic.

## Examples.

### Calling an SQL query directly.

From within an example page, the following PHP code can be used to call the `getInCourse.sql` file, safely injecting the parameters into the SQL, returning the result as an array for outputting to the page.

`page/students-in-course.php`:

```php
use Gt\Database\Client;

$db = new Client();

// Call SQL scripts directly, injecting named parameters:
$studentsInCourse = $db["student"]
	->query("getInCourse", [
		// Inject the SQL parameters as key-value-pairs.
		"course_title" => "Marine Biology",
		"year_since" => 2016
	])->fetchAll();
```

The SQL can join, filter, order, etc. and the query result will be returned in an array to PHP.

`sql/student/getInCourse.sql`:

```sql
select
	`student_id`
	`first_name`,
	`last_name`,
	`date_of_birth`,
	`city`

from
	`student`

where
	`course_title` = :course_title
and
	`year` > :year_since

order by
	`last_name`
```

### Building the query in PHP.

Instead of having a raw SQL query to inject parameters into, the query can be built using chained functions, as shown below.

`page/students-in-course.php`:

```php
use Gt\Database\Client;

$db = new Client();

// Build SQL query dynamically, filtering using chained methods:
$studentsInCourse = $db["student"]
	->where("course_title", "Marine Biology")
	->where("year", ">", 2016)
	->orderBy("last_name")
	->fetchAll();
```

Apart from the SQL schema, there is no need for any SQL queries in the above example - queries are built dynamically and efficiently for you.

### Manipulating SQL results in PHP.

A combination of the two methods of data access can be used to extend existing SQL queries. Using the SQL query from the first example above, the query result can be manipulated further by chained methods as follows:

```php
$studentsInCourse = $db["student"]
	->query("getInCourse", [
		// Inject the SQL parameters as key-value-pairs.
		"course_title" => "Marine Biology",
		"year_since" => 2016
	])
	// Further processing of SQL query in PHP:
	->where("date_of_birth", ">", "1990-01-01")
	->groupBy("city")
	->fetchAll();
```
