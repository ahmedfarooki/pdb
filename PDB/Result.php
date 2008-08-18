<?php

/**
 * Result/Statement class
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   DB
 * @package    PDB
 * @author     Joe Stump <joe@joestump.net> 
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id:$
 * @link       http://www.php.net/pdo
 * @link       http://pear.php.net/package/PDB
 */

/**
 * Result/Statement class for PDB
 *
 * @category   DB
 * @package    PDB
 * @author     Joe Stump <joe@joestump.net> 
 * @copyright  1997-2007 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/PDB
 */
class PDB_Result extends PDOStatement
{
    /**
     * Instance of PDO
     *
     * @access      protected
     * @var         object      $pdo        
     */
    protected $pdo = null;

    /**
     * Constructor
     *
     * @access      protected
     * @param       object      $pdo        Instance of PDO
     * @see         PDB_Result::$pdo
     */
    protected function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Fetch a row from the result set
     *
     * @access      public
     * @param       int         $fetchMode
     * @return      mixed
     */
    public function fetchRow($fetchMode = null)
    {
        if (is_null($fetchMode)) {
            $fetchMode = $this->pdo->fetchMode;
        }

        $res = $this->fetch($fetchMode); 
        return $res;
    }

    /**
     * Fetch a row from result set into $arr
     *
     * @access      public
     * @param       array       $arr
     * @param       int         $fetchMode
     * @return      void
     */
    public function fetchInto(&$arr, $fetchMode = null)
    {
        if (is_null($fetchMode)) {
            $fetchMode = $this->pdo->fetchMode;
        }

        $arr = $this->fetch($fetchMode);        
        if (!$arr) {
            return $arr;
        }

        return true;
    }
}

?>
