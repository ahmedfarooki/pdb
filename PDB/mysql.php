<?php

/**
 * PDB driver for MySQL
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
 * @package     PDB_mysql
 * @author      Joe Stump <joe@joestump.net> 
 * @copyright   1997-2005 The PHP Group
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id:$
 * @link        http://www.php.net/pdo
 * @link        http://pear.php.net/package/PDB
 * @filesource
 */

require_once 'PDB/Common.php';

/**
 * PDB_mysql driver for PDB
 *
 * @category   DB
 * @package    PDB_mysql
 * @author     Joe Stump <joe@joestump.net> 
 * @copyright  1997-2007 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/PDB
 */
class PDB_mysql extends PDB_Common
{
    public function query($sql, array $args = array())
    {
        $attempts = 0;
        do {
            try {
                return parent::query($sql, $args);
            } catch (PDB_Exception $e) {
                $info = $this->errorInfo();
                if ($info[1] == 2006) {
                    $this->reconnect();
                } else {
                    throw $e;
                }
            }
        } while ($attempts++ < 3);

        throw new PDB_Exception('Exhausted retries on query');
    }
}

?>
