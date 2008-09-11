<?php

/**
 * Base PDB class
 *
 * PHP version 5.2+
 *
 * Copyright (c) 2007, Digg, Inc.
 * 
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *  - Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *  - Neither the name of the Digg, INc. nor the names of its contributors 
 *    may be used to endorse or promote products derived from this software 
 *    without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE 
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE 
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR 
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF 
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS 
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN 
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   DB
 * @package    PDB
 * @author     Joe Stump <joe@joestump.net> 
 * @copyright  2007-2008 (c) Digg.com 
 * @license    http://tinyurl.com/42zef New BSD License
 * @version    CVS: $Id:$
 * @link       http://www.php.net/pdo
 * @link       http://pear.php.net/package/PDB
 * @filesource
 */

require_once 'PDB/Exception.php';
require_once 'PDB/Result.php';

/**
 * Base PDB class
 *
 * @category   DB
 * @package    PDB
 * @author     Joe Stump <joe@joestump.net> 
 * @copyright  2007-2008 (c) Digg.com 
 * @license    http://tinyurl.com/42zef New BSD License
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/PDB
 */
abstract class PDB_Common 
{
    /**
     * The PDO connection
     * 
     * Due to various issues with PDO (e.g. the inability to disconnect)
     * we use the decorator pattern to envelope PDO with extra
     * functionality. 
     * 
     * @var object $pdo Instance of PDO
     * @link http://us.php.net/pdo
     * @see PDB_Common::__call()
     */
    protected $pdo = null;

    /**
     * PDO DSN
     *
     * @access protected
     * @var string $dsn PDO DSN (e.g. mysql:host=127.0.0.1;dbname=foo)
     */
    protected $dsn = '';

    /**
     * Username for DB connection
     *
     * @access protected
     * @var string $username DB username
     */
    protected $username = ''; 

    /**
     * Password for DB connection
     *
     * @access protected
     * @var string $password DB password
     */
    protected $password = '';

    /**
     * PDO/Driver options
     *
     * @access protected
     * @var array $options PDO/Driver options
     * @link http://us.php.net/manual/en/pdo.constants.php
     * @link http://us.php.net/manual/en/pdo.drivers.php
     */
    protected $options = array();

    /**
     * Default fetch mode
     *
     * @access      private
     * @var         int         $fetchMode
     */
    public $fetchMode = PDO::FETCH_NUM;

    /**
     * Constructor
     *
     * @param string $dsn      The PDO DSN
     * @param string $username The DB's username
     * @param string $password The DB's password
     * @param array  $options  PDO/driver options array
     *
     * @return void
     * @see PDB_Common::connect(), PDB_Common::$dsn, PDB_Common::$username
     * @see PDB_Common::$password, PDB_Common::$options
     */
    public function __construct($dsn, 
                                $username = '', 
                                $password = '', 
                                $options = array())
    {
        $this->dsn      = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->options  = $options;
        $this->connect();
    }

    /**
     * Connect to the database
     *
     * @return void
     * @see PDB_Common::$dsn, PDB_Common::$username
     * @see PDB_Common::$password, PDB_Common::$options
     * @see PDB_Common::setAttribute
     */
    public function connect()
    {
        $this->pdo = new PDO($this->dsn, 
                             $this->username, 
                             $this->password, 
                             $this->options);

        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Reconnect to the database
     *
     * This reconnects to the database with the given parameters from
     * before we either disconnected or lost the connection. This is useful
     * for when MySQL (and others probably) servers "go away".
     * 
     * @see PDB_Common::disconnect(), PDB_Common::connect()
     * @return void
     */
    public function reconnect()
    {
        $this->disconnect();
        $this->connect();
    }

    /**
     * Disconnect from the DB
     *
     * @return void
     */
    public function disconnect()
    {
        $this->pdo = null;
    }

    /**
     * Implement decorator pattern
     *
     * Originally {@link PDB} was extended from PDO, but this kept us from
     * implementing valid {@link PDB_Common::disconnect()} and 
     * {@link PDB_Common::reconnect()} methods, which were needed for other
     * nice functionality.
     *
     * As a result we use {@link PDB_Common::__call()} to implement the basic
     * decorator pattern. Everything listed below should work without issues.
     *
     * @param string $function Name of function to run
     * @param array  $args     Function's arguments
     *
     * @method bool beginTransaction()
     * @method bool commit()
     * @method string errorCode()
     * @method array errorInfo()
     * @method int exec(string $statement)
     * @method mixed getAttribute(int $attribute)
     * @method string lastInsertId([string $name])
     * @method PDOStatement prepare(string $statement [, array $driver_options])
     * @method string quote(string $string [, int $parameter_type])
     * @method bool rollBack()
     * @return mixed
     */
    public function __call($function, array $args = array()) 
    {
        if (is_null($this->pdo)) {
            throw new PDB_Exception('Not connected to DB');
        }

        return call_user_func_array(array($this->pdo, $function), $args);
    }

    /**
     * Query the database
     *
     * <code>
     * <?php
     * 
     * require_once 'PDB.php';
     *
     * $db = PDB::connect('mysql:host=127.0.0.1;dbname=foo', 'user', 'pass'); 
     * $db->setFetchMode(PDO::FETCH_OBJECT);
     *
     * $sql = 'SELECT * 
     *         FROM items
     *         WHERE promoted = ? AND
     *               userid = ?';
     * 
     * $result = $db->query($sql, array(1, (int)$_GET['userid']));
     *
     * // Notice that {@link PDB_Result} supports object iteration just like
     * // PDOStatement does since it extends from it.
     * foreach ($result as $row) {
     *     echo '<a href="' . $row->url . '">' . $row->title . '</a>' . "\n";
     * }
     *
     * ?>
     * </code> 
     *
     * @param string $sql  The query
     * @param array  $args The query arguments
     *
     * @return object Instance of {@link PDB_Result}
     * @throws {@link PDB_Exception} on failure
     * @link http://us3.php.net/manual/en/class.pdostatement.php
     * @link http://us3.php.net/manual/en/pdostatement.bindparam.php
     */
    public function query($sql, array $args = array())
    {
        try {
            $stmt = $this->prepare($sql, array(
                PDO::ATTR_STATEMENT_CLASS => array(
                    'PDB_Result', array($this->pdo, $this->fetchMode)
                )
            ));

            if (is_array($args)) {
                $cnt = count($args);
                if ($cnt > 0) {
                    foreach ($args as $key => $value) {
                        $param  = (is_int($key) ? ($key + 1) : $key);
                        $result = $stmt->bindParam($param, $args[$key]);
                    }
                }
            }

            $stmt->execute();
            return $stmt;
        } catch (PDOException $error) {
            throw new PDB_Exception($error->getMessage(), $error->getCode());
        }
    }

    /**
     * Fetch a single row
     *
     * <code>
     * <?php
     * 
     * require_once 'PDB.php';
     *
     * $db = PDB::connect('mysql:host=127.0.0.1;dbname=foo', 'user', 'pass'); 
     * $db->setFetchMode(PDO::FETCH_OBJECT);
     *
     * $sql = 'SELECT * 
     *         FROM users
     *         WHERE userid = ?';
     *
     * $user = $db->getRow($sql, array((int)$_GET['userid']));
     * echo 'Welcome back, ' . $user->username . '!';
     *
     * ?>
     * </code>
     * 
     * @param string  $sql       The query to run
     * @param array   $params    The query parameter values
     * @param integer $fetchMode The fetch mode for query
     *
     * @see PDB_Common::query(), PDB_Result
     * @return array
     */
    public function getRow($sql, 
                           array $params = array(), 
                           $fetchMode = null)
    {
        if (is_null($fetchMode)) {
            $fetchMode = $this->fetchMode;
        }

        $result = $this->query($sql, $params);
        return $result->fetchRow($fetchMode);
    }

    /**
     * Fetch a single column
     *
     * <code>
     * <?php
     * 
     * require_once 'PDB.php';
     *
     * $db  = PDB::connect('mysql:host=127.0.0.1;dbname=foo', 'user', 'pass'); 
     * $sql = 'SELECT friendid 
     *         FROM friends
     *         WHERE userid = ?';
     *
     * $friends = $db->getCol($sql, 0, array((int)$_GET['userid']));
     * if (in_array($_SESSION['userid'], $friends)) {
     *    echo 'You are friends with this user!';
     * }
     *
     * ?>
     * </code>
     * 
     * @param string  $sql    The query to run
     * @param integer $col    The column number to fetch (zero-based)
     * @param array   $params The query parameter values
     *
     * @see PDB_Common::query(), PDB_Result
     * @return array
     */
    public function getCol($sql, $col = 0, array $params = array())
    {
        $result = $this->query($sql, $params);
        $ret    = array();
        while ($row = $result->fetchRow(PDO::FETCH_NUM)) {
            $ret[] = $row[$col];
        }

        return $ret;
    }

    /**
     * Fetch all records in query as array
     *
     * This method will fetch all records from a given query into a 
     * numerically indexed array (e.g. $result[0] is the first record).
     *
     * <code>
     * <?php
     * 
     * require_once 'PDB.php';
     *
     * $db = PDB::connect('mysql:host=127.0.0.1;dbname=foo', 'user', 'pass'); 
     * $db->setFetchMode(PDO::FETCH_OBJECT);
     * 
     * $sql = 'SELECT * 
     *         FROM users
     *         WHERE type = ?';
     *
     * $students = $db->getAll($sql, array('student'));
     * foreach ($students as $student) {
     *     echo $student->firstname . "\n";
     * }
     *
     * ?>
     * </code>
     *
     * @param string  $sql       The query to run
     * @param array   $params    The query parameter values
     * @param integer $fetchMode The fetch mode for query
     *
     * @return array
     * @see PDB_Result, PDB_Common::query()
     */
    public function getAll($sql, 
                           array $params = array(), 
                           $fetchMode = null) 
    {
        if (is_null($fetchMode)) {
            $fetchMode = $this->fetchMode;
        }

        $result = $this->query($sql, $params);
        $ret    = array();
        while ($row = $result->fetchRow($fetchMode)) {
            $ret[] = $row;
        }

        return $ret;
    }

    /**
     * Get a single field
     *
     * This will fetch a single value from the first row's first
     * column. 
     *
     * <code>
     * <?php
     * 
     * require_once 'PDB.php';
     *
     * $db  = PDB::connect('mysql:host=127.0.0.1;dbname=foo', 'user', 'pass'); 
     * $sql = 'SELECT COUNT(*) AS total
     *         FROM users
     *         WHERE type = ?';
     *
     * $total = $db->getOne($sql, array('student'));
     * if (!$total) {
     *     echo 'No students!';
     * }
     *
     * ?>
     * </code>
     *
     * @param string $sql    The query to run
     * @param array  $params The query parameter values
     *
     * @see PDB_Common::query(), PDB_Result::fetchRow()
     * @return mixed The value of the first row/column
     */
    public function getOne($sql, array $params = array()) 
    {
        $result = $this->query($sql, $params);
        $row    = $result->fetchRow(PDO::FETCH_NUM);
        return $row[0];
    }

    /**
     * Set the fetch mode for all queries
     *
     * This should be set to one of PDO's fetch modes. Valid values include:
     *  - PDO::FETCH_LAZY
     *  - PDO::FETCH_ASSOC
     *  - PDO::FETCH_NAMED
     *  - PDO::FETCH_NUM
     *  - PDO::FETCH_BOTH
     *  - PDO::FETCH_OBJ
     *
     * @param integer $mode The DB fetch mode
     *
     * @throws UnexpectedArgumentException on invalid modes
     * @access public
     * @return void
     */
    public function setFetchMode($mode)
    {
        switch ($mode) {
        case PDO::FETCH_LAZY:
        case PDO::FETCH_ASSOC:
        case PDO::FETCH_NAMED:
        case PDO::FETCH_NUM:
        case PDO::FETCH_BOTH:
        case PDO::FETCH_OBJ:
            $this->fetchMode = $mode;
            break;
        default:
            throw UnexpectedArgumentException('Invalid mode');
        }
    }

    /**
     * Set an attribute
     *
     * @param integer $attribute The attribute to set
     * @param mixed   $value     The attribute's value
     *
     * @link http://us.php.net/manual/en/pdo.setattribute.php
     * @return true False if something failed to set
     */
    public function setAttribute($attribute, $value)
    {
        if ($this->pdo->setAttribute($attribute, $value)) {
            $this->options[$attribute] = $value;
            return true;
        }

        return false;
    }
}

?>
