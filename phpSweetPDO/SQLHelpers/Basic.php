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

namespace phpSweetPDO\SQLHelpers;

/**
 * Class with basic helpers to build some SQL operators easily
 */
class Basic {
    /**
     * Insert helper routine. Example of use:
     *
     * <code>
     * <?php
     * $connection->execute(BasicHelpers::insert("mytable", array("name" => "John Smith")));
     * </code>
     *
     * @param string $tablename Name of the table to which to insert the data
     * @param array $data Associative array of fieldName => fieldValue to insert into table
     * @return string Generated SQL statement
     *
     */
    public static function insert($tablename, array $data) {
        //Forming initial SQL skeleton INSERT INTO table(field1, field2,...) VAlUES(

        $sql = 'INSERT INTO ' . $tablename . '(' . implode(', ', array_keys($data)) . ') VALUES (';

        //Now making a parameter for each field (field1 => :field1...)
        $sqlFieldParams = array();
        foreach ($data as $fieldName => $fieldValue) {
            $sqlFieldParams [] = ':' . $fieldName;
        }

        //Listing params
        $sql .= implode(', ', $sqlFieldParams) . ')';

        return array($sql, $data);
    }

    /**
     * Update helper rountine. Example of use:
     *
     * <code>
     * <?php
     * $connection->execute(BasicHelpers::update("mytable", array("name" => "John Smith"), "userid=1"));
     * </code>
     *
     * @param string $tablename Name of the table to which to insert the data
     * @param array $data Associative array of fieldName => fieldValue to update on table
     * @param string $criteria WHERE part of the query (without 'WHERE' keyword itself)
     * @return string Generated SQL statement
     *
     */
    public static function update($tablename, array $data, $criteria = false) {
        $sql = 'UPDATE ' . $tablename . ' SET ';
        $sqlFieldParams = array();
        foreach ($data as $fieldName => $fieldValue) {
            $sqlFieldParams [] = $fieldName . '=:' . $fieldName;
        }
        $sql .= implode(', ', $sqlFieldParams) . ($criteria ? ' WHERE ' . $criteria : "");

        return array($sql, $data);
    }
}
