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

namespace Silex\Extension;

use Silex\Application;
use Silex\ExtensionInterface;
use phpSweetPDO;

/**
 * Silex framework extension for phpSweetPDO library.
 */
class PhpSweetPdoExtension implements ExtensionInterface {
    public function register(Application $app) {
        $app['db'] = $app->share(function() use($app) {
                return new phpSweetPDO\Connection($app['db.connection_string'], $app['db.username'], $app['db.password'], isset($app['db.event_dispatcher'])
                            ? $app['db.event_dispatcher'] : null, isset($app['db.driver_options'])
                            ? $app['db.driver_options'] : array());
            });

        if (isset($app['db.class_path'])) {
            $app['autoloader']->registerNamespace('phpSweetPDO', $app['db.class_path']);
        }
    }
}