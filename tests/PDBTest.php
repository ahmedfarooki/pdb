<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * PDB test case
 *
 * PHP version 5
 *
 * @category   Tests
 * @package    PDB
 * @subpackage Tests
 * @author     Ian Eure <ian@digg.com>
 * @copyright  2009 Digg, Inc. All rights reserved.
 * @version    SVN:   $Id$
 * @filesource
 */

require_once 'PDB.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * PDBTest
 *
 * @package    PDB
 * @subpackage Tests
 * @author     Ian Eure <ian@digg.com>
 */
class PDBTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test factory()
     *
     * @dataProvider dsnProvider
     *
     * @return void
     */
    public function testConnect($dsn, $user, $pass, $options)
    {
        $db = PDB::connect($dsn, $user, $pass, $options);
        $this->assertType('PDB_Common', $db);
        $this->assertNotSame($db, PDB::connect($dsn, $user, $pass, $options));
    }

    /**
     * Test connect() with an invalid driver
     *
     * @expectedException PDB_Exception
     *
     * @return void
     */
    public function testConnectInvalidDriver()
    {
        PDB::connect('Fake');
    }

    /**
     * Test connect() with a bad driver
     *
     * @expectedException PDB_Exception
     *
     * @return void
     */
    public function testConnectBadDriver()
    {
        PDB::connect('ExceptionThrower');
    }

    /**
     * Test singleton()
     *
     * @dataProvider dsnProvider
     *
     * @return void
     */
    public function testSingleton($dsn, $user, $pass, $options)
    {
        $db = PDB::singleton($dsn, $user, $pass, $options);
        $this->assertType('PDB_Common', $db);
        $this->assertSame($db, PDB::singleton($dsn, $user, $pass, $options));
    }

    /**
     * DB DSN provider
     *
     * @return array Array of DSNs to test
     */
    public function dsnProvider()
    {
        $dir = '@temp_dir@';
        if (substr($dir, 0, 1) == '@' || !is_writable($dir)) {
            $dir = '/tmp';
        }
        $file = tempnam($dir, __CLASS__ . '.sqliite3');
        return array(array("sqlite:$file", '', '', array()),
                     array('sqlite::memory:', '', '', array()));
    }
}

?>
