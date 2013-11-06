<?php
/*
 * ========================================================================
 * Copyright (c) 2011 Vladislav "FractalizeR" Rastrusny
 * Website: http://www.fractalizer.ru
 * Email: FractalizeR@yandex.ru
 * ------------------------------------------------------------------------
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================================
 */

namespace phpSweetPDO;

use phpSweetPDO\Exceptions\DbException;

/**
 * Class, representing dabatase connection
 */
class Connection {

    /**
     * PDO object
     *
     * @var \PDO
     */
    protected $_pdoObject;

    /**
     * Optional event dispatcher used to track various library events
     *
     * @var \sfEventDispatcher
     */
    protected $_eventDispatcher;

    /**
     * Default constructor.
     *
     * If you provide $eventDispatcher to the constructor, most calls to library's methods will generate appropriate event.
     * Events can be used to track SQL statements executed by the library or for profiling purposes.
     *
     * @param string $connectionString PDO connection string ('mysql:dbname=testdb;host=127.0.0.1')
     * @param string $username Username to use when calling PDO
     * @param string $password Password to use when calling PDO
     * @param \sfEventDispatcher $eventDispatcher Event dispatcher to use when reporting library events. Null if no reporting needed
     * @param array $driverOptions Various driver specific options for PDO connection. Empty array if no options
     *
     */
    public function __construct($connectionString, $username, $password, \Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcher = null,
                                $driverOptions = array()) {
        $this->_eventDispatcher = $eventDispatcher;

        $this->_fireEvent('phpsweetpdo.connect.started', array(
                '$connection_string' => &$connectionString,
                'username'           => &$username,
                'password'           => &$password,
                'driver_options'     => &$driverOptions
            ));

        $this->_pdoObject = new \PDO($connectionString, $username, $password, $driverOptions);

        $this->_fireEvent('phpsweetpdo.connect.finished', array(
                '$connection_string' => &$connectionString,
                'username'           => &$username,
                'password'           => &$password,
                'driver_options'     => &$driverOptions,
                'result'             => $this->_pdoObject
            ));
    }

    /**
     * Execute a statement without returning any data
     *
     * @param string|array $sql SQL statement
     * @param array $params Parameters. A single value or an array of values
     * @param array $driverOptions Specific options to pass to underlying PDO prepare() call. Empty array if no options
     * @return integer A number of rows affected by last operation
     */
    public function execute($sql, $params = array(), array $driverOptions = array()) {
        $this->_modifyParams($sql, $params);

        $this->_fireEvent('phpsweetpdo.execute.started', array(
                'sql'            => &$sql,
                'params'         => &$params,
                'driver_options' => &$driverOptions
            ));

        //Preparing and executing
        $statement = $this->_prepareStatement($sql, $params, $driverOptions);
        $this->_executeStatement($statement, $sql, $params);
        $result = $statement->rowCount();

        $this->_fireEvent('phpsweetpdo.execute.finished', array(
                'sql'            => &$sql,
                'params'         => &$params,
                'driver_options' => &$driverOptions,
                'result'         => &$result
            ));

        return $result;
    }

    /**
     * Executes a select statement and returns data for it.
     *
     * @param string|array $sql SQL statement to execute.
     * @param array|mixed $params Query parameters to substitute instead of ? marks in query
     * @param array $driverOptions Specific options to pass to underlying PDO prepare() call. Empty array if no options
     * @return Recordset The recordset, containing selection results
     */
    public function select($sql, $params = array(), array $driverOptions = array()) {
        $this->_modifyParams($sql, $params);

        $this->_fireEvent('phpsweetpdo.select.started', array(
                'sql'            => &$sql,
                'params'         => &$params,
                'driver_options' => &$driverOptions
            ));

        $statement = $this->_prepareStatement($sql, $params, $driverOptions);

        $result = new Recordset($statement, $params);

        $this->_fireEvent('phpsweetpdo.select.finished', array(
                'sql'            => &$sql,
                'params'         => &$params,
                'driver_options' => &$driverOptions,
                'result'         => &$result
            ));

        return $result;
    }

    /**
     * Function returns a single result from a query or false if no
     * rows were selected by query
     *
     * @param string|array $sql SQL statement to execute.
     * @param array|mixed $params Query parameters to substitute instead of ? marks in query
     * @param array $driverOptions Specific options to pass to underlying PDO prepare() call. Empty array if no options
     * @return mixed|boolean A single value from SQL statement execution or boolean false if no result available
     */
    public function getOneValue($sql, $params = array(), array $driverOptions = array()) {
        $this->_modifyParams($sql, $params);

        $this->_fireEvent('phpsweetpdo.get_one_value.started', array(
                'sql'            => &$sql,
                'params'         => &$params,
                'driver_options' => &$driverOptions
            ));

        $statement = $this->_prepareStatement($sql, $params, $driverOptions);
        $this->_executeStatement($statement, $sql, $params);
        $result = $statement->fetchColumn(0);

        $this->_fireEvent('phpsweetpdo.get_one_value.finished', array(
                'sql'            => &$sql,
                'params'         => &$params,
                'driver_options' => &$driverOptions,
                'result'         => &$result
            ));

        return $result;
    }

    /**
     * Function returns a single row from a query or false if no
     * rows were selected by query
     *
     * @param string|array $sql SQL statement to execute.
     * @param array|mixed $params Query parameters to substitute instead of ? marks in query
     * @param array $driverOptions Specific options to pass to underlying PDO prepare() call. Empty array if no options
     * @return mixed|boolean A single row from SQL statement execution or boolean false if no result available
     */
    public function getOneRow($sql, $params = array(), array $driverOptions = array()) {
        $this->_modifyParams($sql, $params);

        $this->_fireEvent('phpsweetpdo.get_one_row.started', array(
                'sql'            => &$sql,
                'params'         => &$params,
                'driver_options' => &$driverOptions
            ));

        $statement = $this->_prepareStatement($sql, $params, $driverOptions);
        $this->_executeStatement($statement, $sql, $params);

        $result = $statement->fetchObject('\phpSweetPDO\RecordsetRow');

        $this->_fireEvent('phpsweetpdo.get_one_row.finished', array(
                'sql'            => &$sql,
                'params'         => &$params,
                'driver_options' => &$driverOptions,
                'result'         => &$result
            ));

        return $result;
    }


    /**
     * Function begins a transaction
     *
     * @return boolean
     */
    public function beginTransaction() {
        $this->_fireEvent('phpsweetpdo.begin_transaction.started', array());
        $result = $this->_pdoObject->beginTransaction();
        $this->_fireEvent('phpsweetpdo.begin_transaction.finished', array('result' => $result));
        return $result;
    }

    /**
     * Function commits transaction
     *
     * @return boolean
     */
    public function commitTransaction() {
        $this->_fireEvent('phpsweetpdo.commit_transaction.started', array());
        $result = $this->_pdoObject->commit();
        $this->_fireEvent('phpsweetpdo.commit_transaction.finished', array('result' => &$result));
        return $result;
    }

    /**
     * Function rolls back transaction
     *
     * @return boolean
     */
    public function rollbackTransaction() {
        $this->_fireEvent('phpsweetpdo.rollback_transaction.started', array());
        $result = $this->_pdoObject->rollBack();
        $this->_fireEvent('phpsweetpdo.rollback_transaction.finished', array('result' => &$result));
        return $result;
    }

    /**
     * Returns the last inserted ID into the database
     *
     * @param string $sequenceName The optional name of the sequence to return data for
     *
     * @return mixed
     */
    public function getLastInsertId($sequenceName = "") {
        return $this->_pdoObject->lastInsertId($sequenceName);
    }

    /**
     * Quotes the string to be safely used in queries
     *
     * @param  $str String to quote
     * @return string Quoted string
     */
    public function quote($str) {
        return $this->_pdoObject->quote($str);
    }


    /**
     * Closes connection
     *
     * @return void
     */
    public function close() {
        $this->_pdoObject = null;
    }

    /**
     * Destructor to close connection on object destruction.
     */
    public function __destruct() {
        $this->close();
    }

    /**
     * Function to report events to the dispatcher
     *
     * @param string $name A name of the event to fire
     * @param array $params Event parameters
     * @return void
     */
    protected function _fireEvent($name, $params) {
        if (is_null($this->_eventDispatcher)) {
            return;
        }
        $event = new \sfEvent($this, $name, $params);
        $this->_eventDispatcher->notify($event);
    }

    /**
     * Prepares PDOStatement and checks for errors
     *
     * @throws DbException
     * @param string $sql SQL statement. No array allowed here, string only
     * @param array|mixed $params Query parameters to substitute instead of ? marks in query
     * @param array $driverOptions Specific options to pass to underlying PDO prepare() call. Empty array if no options
     * @return \PDOStatement
     */
    protected function _prepareStatement($sql, $params, $driverOptions) {
        $statement = $this->_pdoObject->prepare($sql, $driverOptions);

        if (!$statement) {
            throw new DbException($statement->errorInfo(), $sql, $params);
        }
        return $statement;
    }

    /**
     * Executes PDOStatement and checks for errors
     *
     * @throws DbException
     * @param  \PDOStatement $statement
     * @param  string $sql
     * @param  array $params
     * @return void
     */
    protected function _executeStatement($statement, $sql, $params) {
        if ((!$statement->execute($params)) and ($statement->errorCode() != '00000')) {
            throw new DbException($statement->errorInfo(), $sql, $params);
        }
    }


    /**
     * Unwraps parameters from $sql if it is array and convert single param into array
     *
     * @throws LogicException
     * @param  $sql
     * @param  $params
     * @return void
     */
    protected function _modifyParams(&$sql, &$params) {
        if (is_array($sql)) {
            if (count($sql) < 2) {
                throw new \InvalidArgumentException('If $sql is array, it needs to have at least 2 elements: (0) sql query, string and (1) parameters (single value or array).');
            }
            if ((count($params) > 0)) {
                throw new \InvalidArgumentException('$sql is array, $params should be empty in this case.');
            }
            $params = $sql[1];
            $sql    = $sql[0];
        }
        if (!is_array($params)) {
            $params = array($params);
        }
    }
}