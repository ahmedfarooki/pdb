<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Unit tests for PDB_sqlite
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

require_once 'PDB/TestCase.php';

/**
 * PDB_sqliteTest
 *
 * @package    PDB
 * @subpackage Tests
 * @author     Ian Eure <ian@digg.com>
 */
class PDB_sqliteTest extends PDB_TestCase
{
    protected $createTable = array(
        'CREATE TABLE PDB_sqliteTest (
            first_name char(50) not null,
            last_name char(50) not null,
            state char(2) not null
        )'
    );

    protected $dropTable = 'DROP TABLE IF EXISTS PDB_sqliteTest';


    /**
     * Connect to the DB
     *
     * @return PDB_sqlite
     */
    protected function connect()
    {
        return PDB::connect('sqlite::memory:');
    }
}

?>
