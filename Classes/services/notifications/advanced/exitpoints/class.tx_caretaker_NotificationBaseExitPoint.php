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
class tx_caretaker_NotificationBaseExitPoint implements tx_caretaker_NotificationExitPointInterface
{
    /**
     * @var array
     */
    protected $config = array();

    /**
     * @var string
     */
    protected $template = '
Date/Time: ###DATETIME###
Instance: ###INSTANCE_TITLE### [###INSTANCE_ID###]
Test: ###TEST_TITLE###
State is now: ###STATE_NOW### (since ###STATE_NOW_TIME###)
State before: ###STATE_BEFORE### (was ###STATE_BEFORE_TIME###)
Info:
###TEST_INFO###

----------------------------------------------------
';

    /**
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     */
    protected $cObj;

    /**
     * @param array $notification
     * @param array $config
     * @return void
     */
    public function addNotification($notification, $config)
    {
    }

    /**
     * @param array $config
     * @return void
     */
    public function init(array $config)
    {
        $this->config = $this->flattenFlexformConfig($config);
        if ($this->config['template']) {
            $this->template = $this->config['template'];
        }

        $this->cObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            'TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer'
        );
    }

    /**
     * @return void
     */
    public function execute()
    {
    }

    /**
     * @param array $flexformConfig
     * @return array
     */
    protected function flattenFlexformConfig($flexformConfig)
    {
        $config = array();
        foreach ($flexformConfig['data']['sDEF']['lDEF'] as $key => $value) {
            $config[$key] = $value['vDEF'];
        }

        return $config;
    }

    /**
     * @param array $overrideConfig
     * @return array
     */
    protected function getConfig($overrideConfig)
    {
        $config = $this->config;
        if (is_array($overrideConfig)) {
            $config = $this->config;
            \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($config, $overrideConfig);
        }

        return $config;
    }

    /**
     * @param  array $notification
     * @return array
     */
    protected function getMarkersForNotification($notification)
    {
        /** @var tx_caretaker_AbstractNode $node */
        $node = $notification['node'];
        /** @var tx_caretaker_TestResult $result */
        $result = $notification['result'];
        $ancestorResult = $node->getPreviousDifferingResult($result);
        $ancestorResultPrev = $node->getPreviousDifferingResult($ancestorResult);

        $durationStateBefore = ($result && $ancestorResult->getTimestamp() > 0 && $ancestorResultPrev->getTimestamp() > 0 ?
            $ancestorResult->getTimestamp() - $ancestorResultPrev->getTimestamp() :
            0);
        $durationState = ($result && $result->getTimestamp() > 0 && $ancestorResult->getTimestamp() > 0 ?
            $result->getTimestamp() - $ancestorResult->getTimestamp() :
            0);

        return array(
            '###DATETIME###' => date('Y-m-d H:i:s', $result->getTimestamp()),
            '###INSTANCE_TITLE###' => ($node instanceof tx_caretaker_AbstractNode && $node->getInstance() ?
                '"' . $node->getInstance()->getTitle() . '"' :
                '-'),
            '###INSTANCE_ID###' => ($node instanceof tx_caretaker_AbstractNode ?
                $node->getCaretakerNodeId() :
                '-'),
            '###TEST_TITLE###' => $node->getTitle(),
            '###STATE_NOW###' => ($result ? $result->getLocallizedStateInfo() : ''),
            '###STATE_NOW_TIME###' => ($durationState > 0 ? $this->humanReadableTime($durationState) : ''),
            '###STATE_BEFORE###' => ($ancestorResult ? $ancestorResult->getLocallizedStateInfo() : ''),
            '###STATE_BEFORE_TIME###' => ($durationStateBefore > 0 ? $this->humanReadableTime($durationStateBefore) : ''),
            '###TEST_INFO###' => ($result ? $result->getLocallizedInfotext() : ''),
        );
    }

    /**
     * @param array $notification
     * @return string
     */
    protected function getMessageForNotification($notification)
    {
        return $this->cObj->substituteMarkerArray(
            $this->template,
            $this->getMarkersForNotification($notification)
        );
    }

    /**
     * @param int $time
     * @return string
     */
    protected function humanReadableTime($time)
    {
        $periods = array('sec', 'min', 'hour', 'day', 'week', 'month', 'year', 'decade');
        $lengths = array('60', '60', '24', '7', '4.35', '12', '10');
        for ($j = 0; $time >= $lengths[$j]; $j++) {
            if ($lengths[$j] == 0) {
                break;
            }
            $time /= $lengths[$j];
        }
        $time = round($time);
        if ($time != 1) {
            $periods[$j] .= 's';
        }

        return $time . ' ' . $periods[$j];
    }
}
