<?php

require_once 'PDB/TestCase.php';

class PDB_mysqlTest extends PDB_TestCase
{
    protected $createTable = array(
        'CREATE TABLE PDB_mysqlTest (
            first_name char(50) not null,
            last_name char(50) not null,
            state char(2) not null
        )'
    );

    protected $dropTable = 'DROP TABLE IF EXISTS PDB_mysqlTest';

    /**
     * Connect to the DB we'll be testing
     *
     * @return PDB_mysql
     */
    protected function connect()
    {
        $this->dsn      = $GLOBALS[get_class($this)]['dsn'];
        $this->username = $GLOBALS[get_class($this)]['username'];
        $this->password = $GLOBALS[get_class($this)]['password'];
        return PDB::singleton($this->dsn, $this->username, $this->password);
    }

    /**
     * Test retries
     *
     * This test makes sure that PDB reconnects and retries when the
     * PDB::RECONNECT attribute is true.
     *
     * @expectedException PDB_Exception
     *
     * @return void
     */
    public function testRetries()
    {
        $pdb = $this->getMock('PDB_mysql', array('reconnect', 'getDefaultPDO'),
                              array($this->dsn, $this->username, $this->password),
                              '', false);
        $pdb->expects($this->exactly(4))->method('reconnect');

        $pdo = $this->getMock('PDO', array('prepare'),
                              array('sqlite::memory:', '', ''));
        $msg = "2006 MySQL server has gone away";
        $pdo->expects($this->exactly(4))->method('prepare')
            ->will($this->throwException(new PDB_Exception($msg, 2006)));

        $pdb->expects($this->any())->method('getDefaultPDO')
            ->will($this->returnValue($pdo));
        $this->assertSame($pdb->getDefaultPDO(), $pdo);
        $this->assertSame($pdb->getPDO(), $pdo);

        $pdb->setAttribute(PDB::RECONNECT, true);
        $pdb->query('SELECT * FROM foo');
    }

    /**
     * Test reconnection
     *
     * This test ensures that PDB doesn't reconnect when the
     * PDB::RECONNECT attribute is false.
     *
     * @expectedException PDB_Exception
     *
     * @return void
     */
    public function testReconnection()
    {
        $pdb = $this->getMock('PDB_mysql', array('reconnect', 'getDefaultPDO'),
                              array($this->dsn, $this->username, $this->password),
                              '', false);
        $pdb->expects($this->never())->method('reconnect');

        $pdo = $this->getMock('PDO', array('prepare'),
                              array('sqlite::memory:', '', ''));
        $msg = "2006 MySQL server has gone away";
        $pdo->expects($this->once())->method('prepare')
            ->will($this->throwException(new PDB_Exception($msg, 2006)));

        $pdb->expects($this->any())->method('getDefaultPDO')
            ->will($this->returnValue($pdo));
        $this->assertSame($pdb->getDefaultPDO(), $pdo);
        $this->assertSame($pdb->getPDO(), $pdo);

        $pdb->setAttribute(PDB::RECONNECT, false);
        $pdb->query('SELECT * FROM foo');
    }
}

?>
