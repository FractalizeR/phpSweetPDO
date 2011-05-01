Building using Phing
=======================================

This directory contains phing build file, that can be used to execute tests
and to build phpSweetPDO phar files.

If you don't know what is phing, read this: http://www.phing.info/docs/guide/stable/

Install Phing
---------------------------------------
You can install Phing via PEAR using two commands:

```
pear channel-discover pear.phing.info
pear install phing/phing
```

After this build process can be run like this:

```
Z:\Denwer\usr\local\php5\PEAR\phing.bat -f Z:/Work/PHP/phpSweetPDO/phing/build.xml make-phar-package
```

Phing targets
---------------------------------------

*   run-tests-library to run all library tests
*   run-tests-phar checks if your system can use generated uncompressed phars
*   make-phar-package builds all phar packages

Please note, that in order to build .phar files, you need to set
```
phar.readonly = Off
```
in your php.ini