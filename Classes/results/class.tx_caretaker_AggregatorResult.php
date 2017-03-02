<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2009-2011 by n@work GmbH and networkteam GmbH
 *
 * All rights reserved
 *
 * This script is part of the Caretaker project. The Caretaker project
 * is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * This is a file of the caretaker project.
 * http://forge.typo3.org/projects/show/extension-caretaker
 *
 * Project sponsored by:
 * n@work GmbH - http://www.work.de
 * networkteam GmbH - http://www.networkteam.com/
 *
 * $Id$
 */

/**
 * Combined result of aggregated subnodes.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 */
class tx_caretaker_AggregatorResult extends tx_caretaker_NodeResult
{
    /**
     * Number of subtests with state UNDEFINED
     *
     * @var int
     */
    protected $num_UNDEFINED = 0;

    /**
     * Number of subtests with state OK
     *
     * @var int
     */
    protected $num_OK = 0;

    /**
     * Number of subtests with state ERROR
     *
     * @var int
     */
    protected $num_ERROR = 0;

    /**
     * Number of subtests with state WARNING
     *
     * @var int
     */
    protected $num_WARNING = 0;

    /**
     * Constructor
     *
     * @param int $timestamp
     * @param int $state
     * @param int $num_undefined
     * @param int $num_ok
     * @param int $num_warning
     * @param int $num_error
     * @param mixed $message String or tx_caretaker_ResultMessage object
     * @param array $submessages array of tx_caretaker_ResultMessage objects
     *
     */
    public function __construct($timestamp = 0, $state = tx_caretaker_Constants::state_undefined, $num_undefined = 0, $num_ok = 0, $num_warning = 0, $num_error = 0, $message = '', $submessages = null)
    {
        parent::__construct($timestamp, $state, $message, $submessages);
        $this->num_UNDEFINED = $num_undefined;
        $this->num_OK = $num_ok;
        $this->num_WARNING = $num_warning;
        $this->num_ERROR = $num_error;
    }

    /**
     * Create an undefined result with current timestamp
     *
     * @param mixed $message
     * @return tx_caretaker_AggregatorResult
     */
    public static function undefined($message = 'Result is undefined')
    {
        $ts = time();

        return new self($ts, tx_caretaker_Constants::state_undefined, $undefined = 0, $ok = 0, $warning = 0, $error = 0, $message);
    }

    /**
     * Create a result with current timestamp
     *
     * @param int $state
     * @param int $num_undefined
     * @param int $num_ok
     * @param int $num_warning
     * @param int $num_error
     * @param mixed $message String or tx_caretaker_ResultMessage object
     * @param array $submessages array of tx_caretaker_ResultMessage objects
     * @return tx_caretaker_AggregatorResult
     */
    public static function create($state = tx_caretaker_Constants::state_undefined, $num_undefined = 0, $num_ok = 0, $num_warning = 0, $num_error = 0, $message = '', $submessages = null)
    {
        $timestamp = time();

        return new self($timestamp, $state, $num_undefined, $num_ok, $num_warning, $num_error, $message, $submessages);
    }

    /**
     * Return number of children with state UNDEFINED
     *
     * @return int
     */
    public function getNumUNDEFINED()
    {
        return $this->num_UNDEFINED;
    }

    /**
     * Return number of children with state OK
     *
     * @return int
     */
    public function getNumOK()
    {
        return $this->num_OK;
    }

    /**
     * Return number of children with state WARNING
     *
     * @return int
     */
    public function getNumWARNING()
    {
        return $this->num_WARNING;
    }

    /**
     * Return number of children with state ERROR
     *
     * @return int
     */
    public function getNumERROR()
    {
        return $this->num_ERROR;
    }

    /**
     * Returns the number of children with state that fits the given name.
     *
     * @param string $stateName state name (valid values: UNDEFINED, OK, WARNING, ERROR)
     * @return int
     */
    public function getNumGENERIC($stateName)
    {
        $variableName = 'num_' . strtoupper($stateName);

        return $this->$variableName;
    }

    /**
     * Get a Hash for the given Status. If two results give the same hash they
     * are considered to be equal.
     *
     * @return string ResultHash
     */
    public function getResultHash()
    {
        $state = array(
            'state' => (int)$this->getState(),
            'STATE_UNDEFINED' => (int)$this->getNumUNDEFINED(),
            'STATE_OK' => (int)$this->getNumOK(),
            'STATE_WARNING' => (int)$this->getNumWARNING(),
            'STATE_ERROR' => (int)$this->getNumERROR(),
        );

        return md5(serialize($state));
    }
}
