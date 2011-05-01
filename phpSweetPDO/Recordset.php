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
 * Class representing recordset of database.
 *
 * Class implies to be iterated via foreach construction.
 * If you choose to use other ways to use it, remember to call
 * @see rewind() method to fetch data from DB
 *
 */
class Recordset implements \Iterator, \Countable {

    /**
     * PDO statement object
     *
     * @var PDOStatement
     */
    private $_statement = null;

    /**
     * Query parameters
     *
     * @var array
     */
    private $_params = array();

    /**
     * Object representing the current row in resultset
     *
     * @var RecordsetRow
     */
    private $_currentRowObj = null;

    /**
     * Index of the current row
     *
     * @var int
     */
    private $_currentRowIndex = 0;

    /**
     * Shows if we need to refresh the statement on first @see rewind()
     * @var bool
     */
    private $_needsRefresh = false;

    /**
     * Constructor
     *
     * @param \PDOStatement $statement Statement, that should be wrapped to this recordset
     * @param array $params Query parameters
     */
    public function __construct(\PDOStatement $statement, array $params) {
        $this->_statement = $statement;
        $this->_params = $params;
        $this->_refresh();
    }

    /**
     * Reexecutes recordset query with the same parameters
     * This function is called inside @see rewind()
     *
     */
    protected function _refresh() {
        $this->_statement->execute($this->_params);
        if ($this->_statement->errorCode() !== '00000') {
            throw new DbException($this->_statement->errorInfo(), $this->_statement->queryString);
        }
        $this->_currentRowIndex = 0;
    }

    /**
     *
     * @see Iterator::current()
     * @return null|\RecordsetRow
     */
    public function current() {
        return $this->_currentRowObj;
    }

    /**
     *
     * @see Iterator::key()
     * @return int
     */
    public function key() {
        return $this->_currentRowIndex;
    }

    /**
     *
     * @see Iterator::next()
     * @return null|\RecordsetRow
     */
    public function next() {
        $this->_currentRowObj = $this->_statement->fetchObject('\phpSweetPDO\RecordsetRow');
        if ($this->_statement->errorCode() !== '00000') {
            throw new DbException($this->_statement->errorInfo(), $this->_statement->queryString);
        }
        $this->_currentRowIndex++;
        return $this->_currentRowObj;
    }

    /**
     *
     * @see Iterator::rewind()
     */
    public function rewind() {
        //Dirty hack because rewind() is called before entering the loop
        //and on object creation we already have query executed
        if ($this->_needsRefresh) {
            $this->_refresh();
        } else {
            $this->_needsRefresh = true;
        }
        $this->_currentRowObj = $this->_statement->fetchObject('\phpSweetPDO\RecordsetRow');
        if ($this->_statement->errorCode() !== '00000') {
            throw new DbException($this->_statement->errorInfo(), $this->_statement->queryString);
        }
    }

    /**
     *
     * @see Iterator::valid()
     * @return bool
     */
    public function valid() {
        return $this->_currentRowObj !== false;
    }

    /**
     *
     * @see Countable::count()
     * @return int
     */
    public function count() {
        return $this->_statement->rowCount();
    }

    /**
     * Closes dataset and releases resources
     *
     * @return void
     */
    public function close() {
        $this->_statement->closeCursor();
    }

    /**
     * Destructor
     */
    function __destruct() {
        $this->close();
    }
}