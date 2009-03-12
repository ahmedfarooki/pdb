<?php

$path = '@test_dir@/@package-name@/tests';
if (substr($path, 0, 1) == '@') {
    $path  = realpath(dirname(__FILE__));
    $paths = array($path, realpath(dirname(__FILE__) . '/../'));
} else {
    $paths = array($path);
}

set_include_path(implode(':', $paths) . ':' . get_include_path());

require_once 'tests-config.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PDBTest.php';
require_once 'PDB/mysqlTest.php';
require_once 'PDB/sqliteTest.php';

class PDB_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHPUnit');
        $suite->addTestSuite('PDBTest');
        $suite->addTestSuite('PDB_mysqlTest');
        $suite->addTestSuite('PDB_sqliteTest');
        return $suite;
    }
}

?>
