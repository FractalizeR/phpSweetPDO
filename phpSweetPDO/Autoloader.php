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

/**
 * Simple autoloader for phpSweetPDO
 */
class Autoloader {

    public static function load($className) {
        if (strpos($className, "phpSweetPDO") !== 0) {
            return;
        }

        $pos = strpos($className, '\\');
        $className = substr($className, $pos + 1);

        require_once(__DIR__ . '/' . strtr($className, '\\_', '//') . '.php');
    }

    public static function register() {
        spl_autoload_register(__NAMESPACE__ . '\\Autoloader::load');
    }

}
