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

/**
 * Base PDB class
 *
 * @category   DB
 * @package    PDB
 * @author     Joe Stump <joe@joestump.net> 
 * @copyright  1997-2007 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/PDB
 */
abstract class PDB
{
    /**
     * Singleton connections
     *
     * @access      private
     * @var         array       $singletons
     * @static
     */
    static private $singletons = array();

    /**
     * Connect to a database
     *
     * @access      public
     * @static
     */
    public static function connect($dsn, 
                                   $username = null,
                                   $password = null,
                                   array $options = array()) 
    {
        list($type,) = explode(':', $dsn);

        $file = 'PDB/' . $type . '.php';
        require_once $file;

        $class = 'PDB_' . $type;
        if (!class_exists($class)) {
            throw new PDB_Exception('PDB class not found: ' . $class);
        }

        try {
            $instance = new $class($dsn, $username, $password, $options);
        } catch (PDOException $error) {
            throw new PDB_Exception($error);
        }

        return $instance;
    }

    /**
     * Singleton connections
     *
     * @access      public
     * @param       string      $dsn
     * @param       string      $username
     * @param       string      $password
     * @param       array       $options
     * @return      object      Instance of PDB driver
     * @throws      PDB_Exception
     */
    static public function singleton($dsn,
                                     $username = null,
                                     $password = null,
                                     array $options = array()) 
    {
        $key = md5($dsn . $username . $password . serialize($options));
        if (!isset(self::$singletons[$key])) {
            self::$singletons[$key] = self::connect($dsn, 
                                                    $username,
                                                    $password,
                                                    $options);
        }

        return self::$singletons[$key];
    }
}

?>
