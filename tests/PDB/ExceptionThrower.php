<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * A fake PDB driver for testing, which throws an exception when instantiated.
 *
 * PHP version 5
 *
 * @category   Tests
 * @package    PDB
 * @subpackage Tests
 * @author     Ian Eure <ieure@blarg.net>
 * @copyright  2009 Buster Marx, Inc All rights reserved.
 * @version    SVN:   $Id$
 * @filesource
 * @see        {@link PDB_AllTests::testConnect}
 */

require_once 'PDB/Common.php';

/**
 * PDB_ExceptionThrower
 *
 * @package    PDB
 * @subpackage Tests
 * @author     Ian Eure <ian@digg.com>
 */
class PDB_ExceptionThrower extends PDB_Common
{
    /**
     * Constructor
     *
     * @param
     *
     * @return
     */
    public function __construct($dsn, $username = '', $password = '',
                                $options = array())
    {
        throw new PDOException("This class cannot be instantiated.");
    }
}

?>
