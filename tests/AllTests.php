<?php

$path = '@test_dir@/@package-name@/tests';
if (substr($path, 0, 1) == '@') {
    $path  = realpath(dirname(__FILE__));
    $paths = array(
        $path, realpath(dirname(__FILE__)) . '/../'
    );
} else {
    $paths = array($path);
}

if (!file_exists($path . '/tests-config.php')) {
    die("Unable to find tests-config.php file in $path!\n");    
}

set_include_path(implode(':', $paths) . ':' . get_include_path());

require_once 'tests-config.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PDB/mysqlTest.php';

class AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHPUnit');
        $suite->addTestSuite('PDB_mysqlTest');
        return $suite;
    }
}

?>
