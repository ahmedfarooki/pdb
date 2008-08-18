<?php

/**
 * A simplistic wrapper for PDO
 *
 * PDB is a simplistic wrapper that adds helper functions to PDO. It was
 * creatd in the vain of DB and MDB2, but a pure PHP5/PDO implementation.
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category    DB
 * @package     PDB
 * @author      Joe Stump <joe@joestump.net> 
 * @copyright   1997-2005 The PHP Group
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id:$
 * @link        http://www.php.net/pdo
 * @link        http://pear.php.net/package/PDB
 * @filesource
 */

require_once 'PDB/Exception.php';
require_once 'PDB/Result.php';

abstract class PDB_Common 
{
    /**
     * 
     */
    protected $pdo = null;

    protected $dsn = '';
    protected $password = '';
    protected $username = ''; 
    protected $options = array();

    /**
     * Default fetch mode
     *
     * @access      private
     * @var         int         $fetchMode
     */
    public $fetchMode = PDO::FETCH_NUM;

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

    public function connect()
    {
        $this->pdo = new PDO($this->dsn, 
                             $this->username, 
                             $this->password, 
                             $this->options);

        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function reconnect()
    {
        $this->disconnect();
        $this->connect();
    }

    public function disconnect()
    {
        $this->pdo = null;
    }

    public function __call($function, array $args = array()) 
    {
        if (is_null($this->pdo)) {
            throw new PDB_Exception('Not connected to DB');
        }

        return call_user_func_array(array($this->pdo, $function), $args);
    }

    public function query($sql, array $args = array())
    {
        try {
            $stmt = $this->prepare($sql, array(
                PDO::ATTR_STATEMENT_CLASS => array(
                    'PDB_Result', array($this->pdo)
                )
            ));

            if (is_array($args)) {
                $cnt = count($args);
                if ($cnt > 0) {
                    for ($i = 0 ; $i < $cnt ; $i++) {
                        $result = $stmt->bindParam(($i + 1), $args[$i]);
                    }
                }
            }

            $stmt->execute();
            return $stmt;
        } catch (PDOException $error) {
            throw new PDB_Exception($error->getMessage(), $error->getCode());
        }
    }

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

    public function getCol($sql, $col = 0, array $params = array())
    {
        $result = $this->query($sql, $params);
        $ret = array();
        while ($row = $result->fetchRow(PDO::FETCH_NUM)) {
            $ret[] = $row[$col];
        }

        return $ret;
    }

    public function getAll($sql, 
                           array $params = array(), 
                           $fetchMode = null) {
        if (is_null($fetchMode)) {
            $fetchMode = $this->fetchMode;
        }

        $result = $this->query($sql, $params);
        $ret = array();
        while ($row = $result->fetchRow($fetchMode)) {
            $ret[] = $row;
        }

        return $ret;
    }

    public function getOne($sql, array $params = array()) 
    {
        $result = $this->query($sql, $params);
        $row = $result->fetchRow(PDO::FETCH_NUM);
        return $row[0];
    }

    public function setFetchMode($mode)
    {
        $this->fetchMode = $mode;
    }
}

?>
