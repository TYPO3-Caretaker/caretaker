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
 * A nodeResultRange implementation for testResults.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 */
class tx_caretaker_TestResultRange extends tx_caretaker_NodeResultRange
{
    /**
     * Minimal value of this result range
     *
     * @var float
     */
    public $min_value = 0;

    /**
     * Maximal value of this result range
     *
     * @var float
     */
    public $max_value = 0;

    /**
     * Add a TestResult to the ResultRange
     *
     * @param tx_caretaker_NodeResult $result
     */
    public function addResult($result)
    {
        parent::addResult($result);

        $value = $result->getValue();
        if ($value < $this->min_value) {
            $this->min_value = $value;
        } elseif ($value > $this->max_value) {
            $this->max_value = $value;
        }
    }

    /**
     * Return the minimal result value
     *
     * @return float
     */
    public function getMinValue()
    {
        return $this->min_value;
    }

    /**
     * Return the maximal result value
     *
     * @return float
     */
    public function getMaxValue()
    {
        return $this->max_value;
    }

    /**
     * Get the median value of the given result-set
     * undefined values are ignored
     *
     * @return float
     */
    public function getMedianValue()
    {
        $values = array();
        /** @var tx_caretaker_TestResult $result */
        foreach ($this as $result) {
            $state = $result->getState();
            $value = $result->getValue();
            if (in_array($state, array(
                    tx_caretaker_Constants::state_ok,
                    tx_caretaker_Constants::state_warning,
                    tx_caretaker_Constants::state_error,
                )) && $value > 0
            ) {
                $values[] = $result->getValue();
            }
        }
        sort($values);
        $num = count($values);
        if ($num > 0) {
            if ($num % 2 == 1) {
                $index = (int)(($num - 1) / 2);

                return $values[$index];
            }
            $index = (int)($num / 2);
            $index2 = (int)($num / 2 - 1);

            return ($values[$index] + $values[$index2]) / 2.0;
        }
        return 0;
    }

    /**
     * Get the average value over the time
     * undefined values are ignored
     *
     * @return float
     */
    public function getAverageValue()
    {
        $value_area = 0;
        $value_range = 0;
        $currentResult = null;
        $nextResult = null;

        $this->rewind();
        $currentResult = $this->current();
        $nextResult = $this->next();
        $index = 0;
        while ($currentResult) {
            // start
            if ($currentResult && $nextResult) {
                $timeStart = $currentResult->getTimestamp();
                $timeStop = $nextResult->getTimestamp();
                $value = $currentResult->getValue();
                $state = $currentResult->getState();
                $timeRange = $timeStop - $timeStart;
                if (in_array($state, array(
                        tx_caretaker_Constants::state_ok,
                        tx_caretaker_Constants::state_warning,
                        tx_caretaker_Constants::state_error,
                    )) && $value > 0
                ) {
                    $value_area += $timeRange * $value;
                    $value_range += $timeRange;
                }
            }

            $index++;
            $currentResult = $nextResult;
            $nextResult = $this->next();
        }

        if ($value_range > 0) {
            return $value_area / $value_range;
        }
        return 0;
    }
}
