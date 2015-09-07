## Fyuze Datbase Component

This is the database component for Fyuze. It is a simple wrapper on top of PDO that intends to simplify
interaction with a higher level abstraction. Currently supporting MySQL and SQLite drivers.

## Notes

This documentation is a small excerpt of the [original]() and is purely used for demonstrating using 
outside the framework

## Usage

Once we have a connection instance, you can start interacting with your database using the dbal or query builder.

### Creating Your Connections

    use Fyuze\Database\Agnostic as Db;
    
    // Creates a default connection has no name parameter defined
    $connection = Db::connect([
        'driver' => 'sqlite', // mysql, sqlite
        'database' => ':memory:',
        'fetch' => 'PDO::FETCH_OBJ',
        'charset' => 'UTF8'
    ]);
    
    // 
    $connection = Db::connect([
        'driver' => 'sqlite', // mysql, sqlite
        'database' => ':memory:',
        'fetch' => 'PDO::FETCH_OBJ',
        'charset' => 'UTF8'
    ], 'connection_two');
    
### Accessing your connection

    // Grabs default connection
    $db = Db::first('SELECT 1);
    
    // Grab a specific connection
    $db = Db::connection('connection_two')->first('SELECT 1');
    
#### Available Drivers

Currently supported connections:

- MySQL
- SQLite

#### Available Options

- fetch - string - [fetch type](http://php.net/manual/en/pdostatement.fetch.php)
- persistent - bool
- charset - string

### DBAL



#### Query

`Db::query()` return values are based on the type of query passed. Read queries return an instance of `PDOStatement`, while
write queries return the executed statements affected row count.

    $results = $db->query('SELECT * FROM fyuze');
    
    // Read queries return the statement
    foreach($results->fetchAll() as $obj) {}
    
    // or
    $name = $results->fetch()->name;
    
#### First

`Db::first()` is the shorthand version for doing `Db::query(...)->fetch();`

    $user = $db->first('SELECT * FROM users WHERE username = ?', ['fyuze']);

#### All

`Db::first()` is the shorthand version for doing `Db::query(...)->fetch();`

    $user = $db->first('SELECT * FROM users WHERE username = ?', ['fyuze']);
    
#### Transaction

`Db::transaction()` is providers a cleaner way of performing transactions.

    Db::transaction(function($db) {
        $db->query('INSERT INTO users VALUES (fyuze)');
        $db->query('INSERT INTO users VALUES (matthew)');
    });

A transaction will return `true` on success and `false` on failure.
