<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

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

require_once 'PEAR/Exception.php';

/**
 * Exception class for PDB
 *
 * @category   DB
 * @package    PDB
 * @author     Joe Stump <joe@joestump.net> 
 * @copyright  1997-2007 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/PDB
 */
class PDB_Exception extends PEAR_Exception
{

}

?>
