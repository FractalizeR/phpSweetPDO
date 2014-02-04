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

namespace phpSweetPDO\Exceptions;

/**
 * Database connection exception class
 *
 */
class DbException extends \Exception {

    /** Report only error message */
    const ON_ERROR_REPORT_ERROR_ONLY = 1;

    /** Report error and SQL statement text */
    const ON_ERROR_REPORT_ERROR_AND_SQL_TEXT = 2;

    /** Report error text, sql statement text and SQL parameters */
    const ON_ERROR_REPORT_ALL = 4;

    /**
     * SQLState of exception
     *
     * @var string
     */
    public $sqlState;

    /**
     * Error message description, that comes from driver
     *
     * @var string
     */
    public $driverErrorMessage;

    /**
     * Driver-specific error code
     *
     * @var string
     */
    public $driverErrorCode;

    /**
     * SQL statement that caused the error
     *
     * @var string
     */
    public $sqlStatement;

    /**
     * Parameters that were passed to the statement
     *
     * @var array
     */
    public $sqlParams;

    /**
     * Exception constructor
     *
     * @param array  $errorInfo      Error info array from PDO call
     * @param string $sqlStatement   SQL statement
     * @param array  $sqlParams      Arguments, passed to SQL statement
     * @param int    $errorReporting Error reporting mode
     *
     * @throws \Exception
     */
    public function __construct(array $errorInfo, $sqlStatement = '', $sqlParams = array (),
                                $errorReporting = self::ON_ERROR_REPORT_ERROR_ONLY) {
        $this->sqlState        = $errorInfo[0];
        $this->driverErrorMessage = $errorInfo[2];
        $this->driverErrorCode = $errorInfo[1];
        $this->sqlStatement    = $sqlStatement;
        $this->sqlParams       = $sqlParams;
        $sqlParamsText         = var_export($sqlParams, true);

        switch ($errorReporting) {
            case self::ON_ERROR_REPORT_ERROR_ONLY:
                parent::__construct("Database error [{$errorInfo[0]}]: {$errorInfo[2]}, driver error code is {$errorInfo[1]}");
                break;
            case self::ON_ERROR_REPORT_ERROR_AND_SQL_TEXT:
                parent::__construct("Database error [{$errorInfo[0]}]: {$errorInfo[2]}, driver error code is {$errorInfo[1]}. SQL: $sqlStatement");
                break;
            case self::ON_ERROR_REPORT_ALL:
                parent::__construct("Database error [{$errorInfo[0]}]: {$errorInfo[2]}, driver error code is {$errorInfo[1]} SQL: $sqlStatement Arguments: $sqlParamsText");
                break;
            default:
                throw new \Exception('Invalid error reporting mode!');
        }
    }
}