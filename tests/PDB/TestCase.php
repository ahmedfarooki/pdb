<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PDB.php';

abstract class PDB_TestCase extends PHPUnit_Framework_TestCase
{
    protected $dsn = '';
    protected $username = '';
    protected $password = '';
    protected $pdb = null;

    /**
     * The data we'll use for testing
     *
     * @var array
     * @see insertData()
     */
    protected $testData = array(array('first_name' => 'Joe',
                                      'last_name'  => 'Stump',
                                      'state'      => 'CA'),
                                array('first_name' => 'Jon',
                                      'last_name'  => 'Stump',
                                      'state'      => 'MI'),
                                array('first_name' => 'Mike',
                                      'last_name'  => 'Stump',
                                      'state'      => 'MI'),
                                array('first_name' => 'Susan',
                                      'last_name'  => 'Stump',
                                      'state'      => 'MI'),
                                array('first_name' => 'Laurie',
                                      'last_name'  => 'Appling',
                                      'state'      => 'MI'),
                                array('first_name' => 'Jim',
                                      'last_name'  => 'Stump',
                                      'state'      => 'MI'));


    protected function setUp()
    {
        $this->pdb = $this->connect();
        $this->dropTable();
        $this->createTable();
        $this->insertData();
    }

    protected function createTable()
    {
        $schema = is_array($this->createTable) ? $this->createTable :
            array($this->createTable);
        foreach ($this->createTable as $query) {
            $this->pdb->query($query);
        }
    }

    protected function insertData()
    {
        foreach ($this->testData as $row) {
            $fields = array_keys($row);
            $ph = implode(', ', array_fill(0, count($fields), '?'));
            $sql = 'INSERT INTO ' . get_class($this) . 
                   ' (' . implode(', ', $fields) . ') ' .
                   'VALUES (' . $ph . ')';

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

    /**
     * Fetch mode provider
     *
     * @return array Array of fetch modes and expected result types
     */
    public function fetchModeProvider()
   {
        $lazyClass = version_compare(PHP_VERSION, '5.2.10', '>=') ?
            'PDORow' : 'PDOStatement';
        return array(array(PDO::FETCH_LAZY,  'LAZY',  $lazyClass),
                     array(PDO::FETCH_ASSOC, 'ASSOC', 'array'),
                     array(PDO::FETCH_NAMED, 'NAMED', 'array'),
                     array(PDO::FETCH_NUM,   'NUM',   'array'),
                     array(PDO::FETCH_BOTH,  'BOTH',  'array'),
                     array(PDO::FETCH_OBJ,   'OBJ',   'stdClass'));
    }


    /**
     * Test setFetchMode()
     *
     * @param int   $fetchMode The fetch mode PDB::setFetchMode()
     * @param string $type     The type of data we expect to get back
     *
     * @dataProvider fetchModeProvider
     *
     * @return void
     */
    public function testSetFetchMode($fetchMode, $des, $type)
    {
        $this->pdb->setFetchMode($fetchMode);
        $res = $this->pdb->getRow('SELECT * FROM ' . get_class($this) .
                                  " LIMIT 1");
        $this->assertType($type, $res, "Unexpected type when using FETCH_$des");
    }

    /**
     * Test query()
     *
     * @return void
     */
    public function testQuery()
    {
        $sql = 'SELECT * FROM ' . get_class($this);

        $res = $this->pdb->query($sql);
        $this->assertType('PDOStatement', $res);

        $n = 0;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $this->assertContains($row, $this->testData);
            $n++;
        }
        $this->assertEquals(count($this->testData), $n);
    }

    /**
     * Test getRow()
     *
     * @return void
     */
    public function testGetRow()
    {
        $this->pdb->setFetchMode(PDO::FETCH_ASSOC);
        $row  = $this->testData[array_rand($this->testData)];
        $sql  = "SELECT * FROM " . get_class($this) . " WHERE ";
        $tmp  = array();
        foreach (array_keys($row) as $key) {
            $tmp[] = " $key = ? ";
        }
        $sql .= join(' AND ', $tmp);

        $out = $this->pdb->getRow($sql, array_values($row));
        $this->assertEquals($row, $out);
    }

    /**
     * Get a single value
     *
     * @return void
     */
    public function testGetOne()
    {
        $this->pdb->setFetchMode(PDO::FETCH_ASSOC);
        $row  = $this->testData[array_rand($this->testData)];
        $keys = array_keys($row);
        $col  = $keys[array_rand($keys)];
        $val  = $row[$col];
        $sql  = "SELECT $col FROM " . get_class($this) . " WHERE ";
        $tmp  = array();
        foreach ($keys as $key) {
            $tmp[] = " $key = ? ";
        }
        $sql .= join(' AND ', $tmp);

        $out = $this->pdb->getOne($sql, array_values($row));
        $this->assertEquals($val, $out);
    }

    /**
     * Test getAll()
     *
     * @return void
     */
    public function testGetAll()
    {
        $sql = 'SELECT * FROM ' . get_class($this);

        $all = $this->pdb->getAll($sql, array(), PDO::FETCH_ASSOC);
        $this->assertEquals($this->testData, $all);
    }

    /**
     * Get a single column
     *
     * @return array
     */
    public function testGetCol()
    {
        $cols = array_keys($this->testData[0]);
        $col  = $cols[array_rand($cols)];

        $exp = array();
        foreach ($this->testData as $row) {
            $exp[] = $row[$col];
        }

        $act = $this->pdb->getCol("SELECT $col FROM " . get_class($this));
        $this->assertEquals($exp, $act);
    }

    /**
     * Attribute provider
     *
     * @return array Array of attributes to test setting
     */
    public function attributeProvider()
    {
        return array(array(PDO::ATTR_CASE, PDO::CASE_LOWER),
                     array(PDO::ATTR_CASE, PDO::CASE_NATURAL),
                     array(PDO::ATTR_CASE, PDO::CASE_UPPER),
                     array(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT),
                     array(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING),
                     array(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION),
                     array(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL),
                     array(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING),
                     array(PDO::ATTR_ORACLE_NULLS, PDO::NULL_TO_STRING),
                     array(PDO::ATTR_STRINGIFY_FETCHES, true),
                     array(PDO::ATTR_STRINGIFY_FETCHES, false),
                     array(PDO::ATTR_AUTOCOMMIT, true),
                     array(PDO::ATTR_AUTOCOMMIT, false));
    }

    /**
     * Test setAttribute()
     *
     * @param int   $attr  Attribute to set
     * @param mixed $value Value of the attribute
     *
     * @dataProvider attributeProvider
     *
     * @return void
     */
    public function testSetAttribute($attr, $value)
    {
        try {
            $this->assertTrue($this->pdb->setAttribute($attr, $value));
            if (gettype($value) == 'boolean') {
                $value = (int) $value;
            }
            $this->assertEquals($value, $this->pdb->getAttribute($attr));
        } catch (PDOException $e) {
            if (strstr($e->getMessage(), 'does not support')  ||
                strstr($e->getMessage(), 'cannot be changed for this driver')) {
                $this->markTestSkipped("Unsupported attribute");
            } else {
                throw $e;
            }
        }
    }

    /**
     * Test setAttribute() for PDB-specific attrs
     *
     * @return void
     */
    public function testSetPDBAttribute()
    {
        $pdo = $this->getMock('PDO', array('setAttribute'),
                              array('sqlite::memory:', '', ''));
        $pdo->expects($this->never())->method('setAttribute');
        $this->pdb->accept($pdo);
        $attr = PDB::PDB_ATTRS | 0x01;
        $this->assertTrue($this->pdb->setAttribute($attr, __METHOD__));
        $this->assertEquals(__METHOD__, $this->pdb->getAttribute($attr));
    }
}

?>
