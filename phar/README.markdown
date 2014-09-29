Phar files
=======================================

What is this?
---------------------------------------

This directory contains phpSweetPDO phar archive files. Phars are self-sufficient
and contain all the library files in it. You can use them alone without anything else.

The only difference between three of them is compression.

*   .phar is uncompressed, you can use it with any PHP configuration
that supports .phar files.
*   .phar.bz2 is bzip2 compressed and to work it requires bzip2 extension to be installed.
*   .phar.gz is gzip compressed and to work it requires gzip extension to be present.


Where to get?
---------------------------------------

You can download most recent library phar files from project download area
http://github.com/FractalizeR/phpSweetPDO/archives/master

Or you can use Phing to build them yourself. To do this please read README file in the phing directory.

Usage
---------------------------------------

```php
<?
require_once('phpsweetpdo.phar');
$connection = new \phpSweetPDO\Connection('mysql:dbname=test;host=127.0.0.1', 'root', '');
$connection->close();
```
