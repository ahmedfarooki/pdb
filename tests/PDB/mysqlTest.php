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

    protected $data = array(
        array(
            'first_name' => 'Joe',
            'last_name'  => 'Stump',
            'state'      => 'CA'
        ),

        array(
            'first_name' => 'Jon',
            'last_name'  => 'Stump',
            'state'      => 'MI'
        ),

        array(
            'first_name' => 'Mike',
            'last_name'  => 'Stump',
            'state'      => 'MI'
        ),

        array(
            'first_name' => 'Susan',
            'last_name'  => 'Stump',
            'state'      => 'MI'
        ),

        array(
            'first_name' => 'Laurie',
            'last_name'  => 'Appling',
            'state'      => 'MI'
        ),

        array(
            'first_name' => 'Jim',
            'last_name'  => 'Stump',
            'state'      => 'MI'
        )
    );

    public function testSetFetchMode()
    {
        $this->pdb->setFetchMode(PDO::FETCH_ASSOC);
        $sql = "SELECT *
                FROM " . get_class($this) . "
                WHERE first_name = ? AND last_name = ?";

        $row = $this->pdb->getRow($sql, array(
            $this->data[0]['first_name'],
            $this->data[0]['last_name']
        ));

        $this->assertEquals($this->data[0], $row, 'PDO::FETCH_ASSOC failed'); 

        $this->pdb->setFetchMode(PDO::FETCH_OBJ);
        $sql = "SELECT *
                FROM " . get_class($this) . "
                WHERE first_name = ? AND last_name = ?";

        $row = $this->pdb->getRow($sql);
        $this->assertTrue(isset($row->first_name), 'first_name missing in object');
        $this->assertTrue(isset($row->last_name), 'last_name missing in object');
        $this->assertTrue(isset($row->state), 'state missing in object');
        $this->assertEquals($this->data[0]['first_name'], $row->first_name, 'PDO::FETCH_OBJ failed on first_name'); 
        $this->assertEquals($this->data[0]['last_name'], $row->last_name, 'PDO::FETCH_OBJ failed on last_name'); 
        $this->assertEquals($this->data[0]['state'], $row->state, 'PDO::FETCH_OBJ failed on state'); 
    }

    public function testQuery()
    {
        $sql = 'SELECT state
                FROM ' . get_class($this);

        $res = $this->pdb->query($sql);
        $states = array();
        foreach ($res as $row) {
            $states[] = $row[0];
        }

        $exp = array();
        foreach ($this->data as $row) {
            $exp[] = $row['state'];
        }

        $this->assertEquals($exp, $states);
    }

    public function testGetRow()
    {
        $this->pdb->setFetchMode(PDO::FETCH_ASSOC);
        $sql = "SELECT *
                FROM " . get_class($this) . "
                WHERE first_name = ? AND last_name = ?";

        $row = $this->pdb->getRow($sql, array(
            $this->data[0]['first_name'],
            $this->data[0]['last_name']
        ));

        $this->assertTrue((count($row) == 3), 'Unexpected data returned');
        foreach ($row as $key => $value) {
            $this->assertEquals($this->data[0][$key], $value, 'Unexpected value in ' . $key);
        }
    }

    public function testGetOne()
    {
        $sql = 'SELECT first_name
                FROM ' . get_class($this). '
                WHERE first_name = ? AND last_name = ?';

        $firstName = $this->pdb->getOne($sql, array(
            $this->data[0]['first_name'],
            $this->data[0]['last_name']
        ));

        $this->assertTrue(is_string($firstName), 'Name is not a string'); 
        $this->assertEquals($this->data[0]['first_name'], $firstName, 'Names do not match');
    }

    public function testGetAll()
    {
        $sql = 'SELECT *
                FROM ' . get_class($this);

        $all = $this->pdb->getAll($sql, array(), PDO::FETCH_ASSOC);
        $this->assertEquals($this->data, $all);
    }

    public function testGetCol()
    {
        $sql = 'SELECT first_name, state
                FROM ' . get_class($this);

        $states = $this->pdb->getCol($sql, 1);

        $exp = array();
        foreach ($this->data as $row) {
            $exp[] = $row['state'];
        }

        $this->assertEquals($exp, $states);
    }
}

?>
