# Separates database logic through a simple interface.

Encloses your application's database scripts within a simple and standardised interface, separating database access from your page logic.

***

// TODO: Add Shields.io badges.

## Example usage.

### Calling an SQL query.

From within an example page, the following PHP code can be used to call the `getInCourse.sql` file, safely injecting the parameters into the SQL, returning the result as an array for outputting to the page.

`src/page/students.php`:

```php
use \Gt\Database\Client;
use \Gt\Database\Connection\Settings;

$settings = new Settings(
	Settings::DRIVER_MYSQL,
	"MyDatabaseName",
	"localhost",
	"db_username",
	"p4ssw0rd"
);

$db = new Client($settings);

$query = $db["student"]->query("getInCourse", [
	// Inject the SQL parameters as key-value-pairs.
	"course_title" => "Marine Biology",
	"year_since" => 2016
]);

foreach($query->fetchAll() as $student) {
	// ...
}
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

## Database engine compatibility.

Compatibility is provided for the following database providers:

+ MySQL
+ Postgres
+ SQLite
+ SQL Server
+ Mongo (planned)
+ CouchDB (planned)

# Building queries in PHP.

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
## Manipulating SQL results in PHP.

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
