What is phpSweetPDO?
=======================================

phpSweetPDO is a php PDO wrapper, which is:

*   Extremly simple to use
*   Phar package to ease deployment. You just need one 9Kb file to work with library (compressed phar)
*   Compact (like 400 lines of commented code which you may compress even further)
*   Optionally supports events, that allow easy profiling and debugging

Examples of use:
=======================================

```php

<?php
require_once('phpsweetpdo.phar');

//Connecting
$connection = new \phpSweetPDO\Connection('mysql:dbname=test;host=127.0.0.1', 'root', 'password');

//Executing DDL
$connection->execute("DROP TABLE IF EXISTS `phpsweetpdo`");

//Selecting only one value
$value = $this->connection->getOneValue("SELECT field2 FROM phpsweetpdo WHERE id=? AND field2 <> ?", array(1, 300));
echo $value;

//Selecing only one row
$record = $this->connection->getOneRow("SELECT * FROM phpsweetpdo WHERE id=:id1 AND field2<>:id2",
                                                 array('id1' => 1, 'id2' => 300));
echo $record->field1 . $record->field2; //Will throw exception if fields do not exist in a row

//Selecting more than 1 row
$recordset = $connection->select("SELECT * FROM phpsweetpdo ORDER BY field1 ASC");
foreach ($recordset as $currentRow) {
    echo $currentRow->id; //Will throw exception if field id does not exist in recordset
}

//Output parameters of stored procedures
$this->connection->execute("CALL phpsweetpdo_out(@test)");
$result = $this->connection->getOneValue("SELECT @test");

//INSERT and UPDATE build helpers
use phpSweetPDO\SQLHelpers\Basic as Helpers;
$sql = Helpers::insert('mytable', array('field_name' => 'field_value'));
$connection->execute($sql); // INSERT INTO mytable (field_name) VALUES (:field_name); //:field_name = 'field_value'

$sql = Helpers::update('mytable', array('field_name' => 'field_value'), "field_2=13");
$connection->execute($sql); // UPDATE test SET field_name=:field_name WHERE field_2='13'; //:field_name = 'field_value'
```

Events
=======================================
If you pass sfEventDispatcher to the constructor of Connection class, it will fire events on it's actions.

You can read more about event dispatcher here: http://components.symfony-project.org/event-dispatcher/documentation.

The following events can be tracked down:

*    phpsweetpdo.connect.started **/** phpsweetpdo.connect.finished
*    phpsweetpdo.execute.started **/** phpsweetpdo.execute.finished
*    phpsweetpdo.select.started **/** phpsweetpdo.select.finished
*    phpsweetpdo.get_one.value_started **/** phpsweetpdo.get_one_value.finished
*    phpsweetpdo.get_one.row_started **/** phpsweetpdo.get_one_row.finished
*    phpsweetpdo.begin_transaction.started **/** phpsweetpdo.begin_transaction.finished
*    phpsweetpdo.commit_transaction.started **/** phpsweetpdo.commit_transaction.finished
*    phpsweetpdo.rollback_transaction.started **/** phpsweetpdo.rollback_transaction.finished

Most events is accompanied by parameters. They are mostly 'sql' (sql query which is executing), 'params' (parameters,
passed to query), and 'driver_options' - driver options used, if any.


```php
<?php

public function onEvent(sfEvent $event) {
    echo $event->getName();
    $params = $event->getParameters();
    echo 'SQL query is ' . $params['sql'];
}

$eventDispatcher = new sfEventDispatcher();
$eventDispatcher->connect('phpsweetpdo.select.started', 'onEvent');
$eventDispatcher->connect('phpsweetpdo.select.finished', 'onEvent');

$this->_connection = new \phpSweetPDO\Connection('mysql:dbname=test;host=127.0.0.1', 'root', '', $eventDispatcher);
$this->_connection->select("SELECT * FROM phpsweetpdo ORDER BY field1 ASC");
//At this point our onEvent() function will be called twice with respected events and will print the query,
//we tried to execute.
```


Limitations
=======================================

phpSweetPDO does not support (at least yet, send pull requests):

*   Typed PDO parameters. All params are passed as is to PDOStatement->execute() (For example, MySQL deals with type
conversion itself. The same probably goes to many other database engines).
*   Explicit output parameters. You cannot mark a parameter as output in phpSweetPDO call. But you can use
a method described above in the examples section.


Requirements
=======================================

*   PHP 5.3 or higher (you can use lower versions, but the code will need to get cleaned from namespaces then).