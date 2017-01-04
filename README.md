# Database logic organisation for PHP 7 projects.

Encloses your application's database scripts within a simple and standardised interface, separating database access from your page logic.

***

// TODO: Add Shields.io badges.

## Example usage

### Calling an SQL query

From within an example page, the following PHP code can be used to call the `getInCourse.sql` file, safely injecting the parameters into the SQL, returning the result as an array for outputting to the page.

`src/page/students.php`:

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
$resultSet = $db["blog"]->retrieveCommentsForBlog([
	"id_blog" => 123,
]);

// Iterate over all rows.
foreach($resultSet as $comment) {
	$this->doSomethingWithCommentContent(
		$comment["content"])
	);
}

// When you only need one row, access the first row of the ResultSet directly:
$this->doSomethingWithCommentContent(
	$resultSet["content"]
);

```

## Database migrations

This repository can be used to perform database migrations whether or not other database access layers are used.

In the `[database]` section of the project's `config.ini`, set the relative path to the directory containing your migrations, along with the connection settings:

```ini
[database]
migration_path=src/query/_migration
dsn=mysql
schema_name=my_database
host=localhost
username=admin
password=
```

Alternatively, set the following environment variables:

+ `database_migration_path`
+ `database_schema_name`
+ `database_dsn`
+ `database_host`
+ `database_username`
+ `database_password`

Within the `_migration` directory, prefix the queries numerically so they are [natrually ordered](http://php.net/manual/en/function.natsort.php). A `_migration` table will be created on the database to store the last-executed migration number.

## Compatible database engines

Compatibility is provided for the following database providers:

* MySQL
* Postgres
* SQLite
* SQL Server
* Mongo (planned)
* CouchDB (planned)

Contains your application's database scripts within a simple and standardised interface, separating database access from your page logic.

## Examples

### Calling an SQL query directly

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

`src/query/student/getInCourse.sql`:

```sql
select
	`id_student`
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

# Building queries in PHP

Instead of having a raw SQL query to inject parameters into, the query can be built in PHP. As an example, we will replace `src/query/student/getInCourse.sql` with `src/query/student/getInCourse.php`.

`src/query/student/getInCourse.php`:

```php
namespace App\Query\Student;

class GetInCourseQuery extends \Gt\Query\Builder {

public function go() {
	$studentTable = $this->table("student");
	return $studentTable->select(
		"id_student", "first_name", "last_name", "date_of_birth", "city")
		->where("course_title", "Marine Biology")
		->where("year", ">", 2016)
		->orderBy("last_name");
}

}#
```
## Manipulating SQL results in PHP

A combination of the two methods of data access can be used to extend existing SQL queries. Using the SQL query from the first example above, the query result can be manipulated further by chained methods as follows:

```php
`src/query/student/getInCourse.php`:

```php
namespace App\Query\Student;

class GetInCourseQuery extends \Gt\Query\Builder {

public function go() {
	$query = $this->query("getInCourse", [
		// Inject the SQL parameters as key-value-pairs.
		"course_title" => "Marine Biology",
		"year_since" => 2016,
	]);

	$query = $query->->select(
		"course.*",
		"id_student", "first_name", "last_name", "date_of_birth", "city")
		->join("course", "student.id_course", "=", "course.id_course");
}

}#
```
