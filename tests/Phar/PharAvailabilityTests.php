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

/**
 * @runTestsInSeparateProcesses
 */
class UncompressedPharTest extends PHPUnit_Framework_TestCase {

    private function performTests() {
        $connection = new \phpSweetPDO\Connection('mysql:dbname=test;host=127.0.0.1', 'root', '');
        $connection->close();

        $sql = \phpSweetPDO\SQLHelpers\Basic::insert('test', array('id' => 'test'));
        $this->assertEquals("INSERT INTO test(id) VALUES (:id)", $sql[0]);
        $this->assertEquals(array('id' => 'test'), $sql[1]);
    }

    public function testUncompressedPhar() {
        if (!file_exists(__DIR__ . '/../../phar/phpsweetpdo.phar')) {
            $this->markTestSkipped("phpsweetpdo.phar is not build! Please use phing with 'make-phar-package' target to build phar first!");
        }

        require_once(__DIR__ . '/../../phar/phpsweetpdo.phar');

        $this->performTests();
    }

    public function testGzipPhar() {
        if (!file_exists(__DIR__ . '/../../phar/phpsweetpdo.phar.gz')) {
            $this->markTestSkipped("phpsweetpdo.phar is not build! Please use phing with 'make-phar-package' target to build phar first!");
        }

        require_once(__DIR__ . '/../../phar/phpsweetpdo.phar.gz');

        $this->performTests();
    }

    public function testBZip2Phar() {
        if (!file_exists(__DIR__ . '/../../phar/phpsweetpdo.phar.bz2')) {
            $this->markTestSkipped("phpsweetpdo.phar is not build! Please use phing with 'make-phar-package' target to build phar first!");
        }

        require_once(__DIR__ . '/../../phar/phpsweetpdo.phar.bz2');

        $this->performTests();
    }
}
