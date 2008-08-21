<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PDB.php';

abstract class PDB_TestCase extends PHPUnit_Framework_TestCase
{
    protected $dsn = '';
    protected $username = '';
    protected $password = '';
    protected $pdb = null;

    public function __construct()
    {
        $this->dsn      = $GLOBALS[get_class($this)]['dsn'];
        $this->username = $GLOBALS[get_class($this)]['username'];
        $this->password = $GLOBALS[get_class($this)]['password'];
        $this->pdb = PDB::connect($this->dsn, $this->username, $this->password);
    }

    protected function setUp()
    {
        $this->dropTable(); // Clear out old tables
        $this->createTable();
        $this->insertData();
    }

    protected function createTable()
    {
        if (is_array($this->createTable)) {
            foreach ($this->createTable as $query) {
                $this->pdb->query($query);
            }
        } else {
            $this->pdb->query($this->createTable);
        }
    }

    protected function insertData()
    {
        foreach ($this->data as $row) {
            $fields = array_keys($row);
            $sql = 'INSERT INTO ' . get_class($this) . 
                   ' (' . implode(', ', $fields) . ') ' .
                   'VALUES (' . implode(', ', array_fill(0, count($fields), '?')) . ')';

            $this->pdb->query($sql, array_values($row));
        }
    }

    protected function dropTable()
    {
        $this->pdb->query($this->dropTable);
    }

    protected function tearDown()
    {
        $this->dropTable();
        $this->pdb->disconnect();
        $this->pdb = null;
    }
}

?>
