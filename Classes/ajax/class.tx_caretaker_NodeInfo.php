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

use TYPO3\CMS\Core\Core\Environment;

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
 * Ajax methods which are used as ajaxID-methods by the
 * caretaker-overview backend-module.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 */
class tx_caretaker_NodeInfo
{
    const PATH_CHARTS = 'typo3temp/caretaker/charts';

    /**
     * @param array $params
     * @param \TYPO3\CMS\Core\Http\AjaxRequestHandler $ajaxObj
     */
    public function ajaxGetNodeInfo($params, &$ajaxObj)
    {
        $node_id = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('node');
        $node_repository = tx_caretaker_NodeRepository::getInstance();
        $node = $node = $node_repository->id2node($node_id, true);

        if ($node_id && $node) {
            $pathnode = $node;
            $pathparts = array();
            while ($pathnode) {
                $pathparts[] = $pathnode->getTitle();
                $pathnode = $pathnode->getParent();
            }
            $pathinfo = implode(' -&gt; ', array_reverse($pathparts));

            switch (get_class($node)) {
                // test Node
                case 'tx_caretaker_TestNode':
                    /** @var tx_caretaker_TestNode $node */
                    $interval_info = '';
                    $interval = $node->getInterval();
                    if ($interval < 60) {
                        $interval_info .= $interval . ' Seconds';
                    } elseif ($interval < 60 * 60) {
                        $interval_info .= ($interval / 60) . ' Minutes';
                    } elseif ($interval < 60 * 60 * 24) {
                        $interval_info .= ($interval / (60 * 60)) . ' Hours';
                    } else {
                        $interval_info .= ($interval / 86400) . ' Days';
                    }

                    if ($node->getStartHour() || $node->getStopHour() > 0) {
                        $interval_info .= ' [';
                        if ($node->getStartHour()) {
                            $interval_info .= ' after:' . $node->getStartHour();
                        }
                        if ($node->getStopHour()) {
                            $interval_info .= ' before:' . $node->getStopHour();
                        }
                        $interval_info .= ' ]';
                    }

                    $result = $node->getTestResult();
                    $info = '<div class="tx_caretaker_node_info tx_caretaker_node_info_state_' . strtolower($result->getStateInfo()) . '">' .
                        'Title: ' . $node->getTitle() . '<br/>' .
                        'Path: ' . $pathinfo . '<br/>' .
                        'NodeID: ' . $node->getCaretakerNodeId() . '<br/>' .
                        'Type: ' . $node->getTypeDescription() . '<br/>' .
                        'Interval: ' . $interval_info . '<br/>' .
                        'Description: ' . $node->getDescription() . '<br/>' .
                        'Configuration: ' . $node->getConfigurationInfo() . '<br/>' .
                        'Hidden: ' . $node->getHiddenInfo() . '<br/>' .
                        'last Run: ' . strftime('%x %X', $result->getTimestamp()) . '<br/>' .
                        'State: ' . $result->getLocallizedStateInfo() . '<br/>' .
                        'Value: ' . $result->getValue() . '<br/>' .
                        'Message: ' . '<br/>' . nl2br($result->getLocallizedInfotext()) . '<br/>' .
                        '</div>';
                    break;
                default:
                    // aggregator Node
                    $result = $node->getTestResult();
                    $info = '<div class="tx_caretaker_node_info tx_caretaker_node_info_state_' . strtolower($result->getStateInfo()) . '">' .
                        'Title: ' . $node->getTitle() . '<br/>' .
                        'Path: ' . $pathinfo . '<br/>' .
                        'NodeID: ' . $node->getCaretakerNodeId() . '<br/>' .
                        'Description: ' . $node->getDescription() . '<br/>' .
                        'Hidden: ' . $node->getHiddenInfo() . '<br/>' .
                        'last Run: ' . strftime('%x %X', $result->getTimestamp()) . '<br/>' .
                        'State: ' . $result->getLocallizedStateInfo() . '<br/>' .
                        'Message:' . '<br/>' . nl2br($result->getLocallizedInfotext()) . '<br/>' .
                        '</div>';
                    break;
            }
            echo $info;
        } else {
            echo 'please select a node';
        }
    }

    /**
     * @param array $params
     * @param \TYPO3\CMS\Core\Http\AjaxRequestHandler $ajaxObj
     */
    public function ajaxRefreshNode($params, &$ajaxObj)
    {
        $node_id = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('node');
        $force = (bool)\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('force');
        $node_repository = tx_caretaker_NodeRepository::getInstance();
        $node = $node_repository->id2node($node_id, true);

        if ($node_id && $node) {
            $result = $node->updateTestResult(array('forceUpdate' => $force));
            $content = array(
                'state' => $result->getState(),
                'state_info' => $result->getStateInfo(),
                'timestamp' => $result->getTimestamp(),
                'message' => $result->getLocallizedInfotext(),
            );
            $ajaxObj->setContent($content);
            $ajaxObj->setContentFormat('jsonbody');
        } else {
            echo 'please give a valid node id';
        }

        $notificationServices = tx_caretaker_ServiceHelper::getAllCaretakerNotificationServices();
        /** @var tx_caretaker_NotificationServiceInterface $notificationService */
        foreach ($notificationServices as $notificationService) {
            $notificationService->sendNotifications();
        }
    }

    /**
     * @param array $params
     * @param \TYPO3\CMS\Core\Http\AjaxRequestHandler $ajaxObj
     */
    public function ajaxNodeSetAck($params, &$ajaxObj)
    {
        $node_id = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('node');
        $node_repository = tx_caretaker_NodeRepository::getInstance();
        $node = $node_repository->id2node($node_id, true);

        if ($node_id && $node) {
            if ($node instanceof tx_caretaker_TestNode) {
                /** @var tx_caretaker_TestNode $node */
                $result = $node->setModeAck();
                $content = array(
                    'state' => $result->getState(),
                    'state_info' => $result->getStateInfo(),
                    'timestamp' => $result->getTimestamp(),
                    'message' => $result->getLocallizedInfotext(),
                );

                $ajaxObj->setContent($content);
                $ajaxObj->setContentFormat('jsonbody');
            } else {
                echo 'please give a testnode id' . $node_id;
            }
        } else {
            echo 'please give a valid node id';
        }

        $notificationServices = tx_caretaker_ServiceHelper::getAllCaretakerNotificationServices();
        /** @var tx_caretaker_NotificationServiceInterface $notificationService */
        foreach ($notificationServices as $notificationService) {
            $notificationService->sendNotifications();
        }
    }

    /**
     * @param array $params
     * @param \TYPO3\CMS\Core\Http\AjaxRequestHandler $ajaxObj
     */
    public function ajaxNodeSetDue($params, &$ajaxObj)
    {
        $node_id = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('node');
        $node_repository = tx_caretaker_NodeRepository::getInstance();

        $node = $node_repository->id2node($node_id, true);
        if ($node_id && $node) {
            if ($node instanceof tx_caretaker_TestNode) {
                /** @var tx_caretaker_TestNode $node */
                $result = $node->setModeDue();
                $content = array(
                    'state' => $result->getState(),
                    'state_info' => $result->getStateInfo(),
                    'timestamp' => $result->getTimestamp(),
                    'message' => $result->getLocallizedInfotext(),
                );

                $ajaxObj->setContent($content);
                $ajaxObj->setContentFormat('jsonbody');
            } else {
                echo 'please give a testnode id' . $node_id;
            }
        } else {
            echo 'please give a valid node id' . $node_id;
        }

        // send aggregated notifications
        $notificationServices = tx_caretaker_ServiceHelper::getAllCaretakerNotificationServices();
        /** @var tx_caretaker_NotificationServiceInterface $notificationService */
        foreach ($notificationServices as $notificationService) {
            $notificationService->sendNotifications();
        }
    }

    /**
     * @param array $params
     * @param \TYPO3\CMS\Core\Http\AjaxRequestHandler $ajaxObj
     */
    public function ajaxGetNodeGraph($params, &$ajaxObj)
    {
        $node_id = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('node');
        $duration = (int)\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('duration');
        $date_stop = time();
        $date_start = $date_stop - $duration;
        $node_repository = tx_caretaker_NodeRepository::getInstance();
        $node = $node_repository->id2node($node_id, true);

        if ($node_id && $node) {
            $result_range = $node->getTestResultRange($date_start, $date_stop);
            if ($result_range->count()) {
                if (!is_dir(Environment::getPublicPath() . '/' . self::PATH_CHARTS)) {
                    if (!mkdir(Environment::getPublicPath() . '/' . self::PATH_CHARTS, 0770, true)) {
                        throw new \TYPO3\CMS\Core\Cache\Exception('can\'t create path "' . Environment::getPublicPath() . '/' . self::PATH_CHARTS . '"', 1465993775);
                    }
                }
                $filename = self::PATH_CHARTS . DIRECTORY_SEPARATOR . $node_id . '_' . $duration . '.png';
                $base_url = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SITE_URL');

                if ($node instanceof tx_caretaker_TestNode) {
                    $TestResultRangeChartRenderer = new tx_caretaker_TestResultRangeChartRenderer();
                    $TestResultRangeChartRenderer->setTitle($node->getTitle());
                    $TestResultRangeChartRenderer->setTestResultRange($result_range);
                    $result = $TestResultRangeChartRenderer->getChartImageTag($filename, $base_url);

                    if ($result) {
                        echo $result;
                    }
                } elseif ($node instanceof tx_caretaker_AggregatorNode) {
                    $AggregatorResultRangeChartRenderer = new tx_caretaker_AggregatorResultRangeChartRenderer();
                    $AggregatorResultRangeChartRenderer->setTitle($node->getTitle());
                    $AggregatorResultRangeChartRenderer->setAggregatorResultRange($result_range);
                    $result = $AggregatorResultRangeChartRenderer->getChartImageTag($filename, $base_url);

                    if ($result) {
                        echo $result;
                    }
                }
            } else {
                echo 'not enough results';
            }
        } else {
            echo 'please give a valid node id';
        }
    }

    /**
     * @param array $params
     * @param \TYPO3\CMS\Core\Http\AjaxRequestHandler $ajaxObj
     */
    public function ajaxGetNodeLog($params, &$ajaxObj)
    {
        $node_id = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('node');
        $node_repository = tx_caretaker_NodeRepository::getInstance();
        $node = $node_repository->id2node($node_id, true);

        if ($node_id && $node) {
            $start = (int)\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('start');
            $limit = (int)\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('limit');

            $count = $node->getTestResultNumber();
            $results = $node->getTestResultRangeByOffset($start, $limit);

            $content = array(
                'totalCount' => $count,
                'logItems' => array(),
            );

            $logItems = array();
            $i = 0;
            foreach ($results as $result) {
                $i++;
                $logItems[] = array(
                    'num' => $i,
                    'title' => 'title_' . rand(),
                    'timestamp' => $result->getTimestamp(),
                    'stateinfo' => $result->getStateInfo(),
                    'stateinfo_ll' => $result->getLocallizedStateInfo(),
                    'message' => $result->getMessage()->getText(),
                    'message_ll' => $result->getLocallizedInfotext(),
                    'state' => $result->getState(),
                );
            }
            $content['logItems'] = array_reverse($logItems);

            $ajaxObj->setContent($content);
            $ajaxObj->setContentFormat('jsonbody');
        }
    }

    /**
     * @param array $params
     * @param \TYPO3\CMS\Core\Http\AjaxRequestHandler $ajaxObj
     */
    public function ajaxGetNodeProblems($params, &$ajaxObj)
    {
        $node_id = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('node');
        $node_repository = tx_caretaker_NodeRepository::getInstance();
        $node = $node_repository->id2node($node_id, true);

        if ($node_id && $node) {
            if ($node instanceof tx_caretaker_AggregatorNode) {
                $testChildNodes = $node->getTestNodes();
            } elseif ($node instanceof tx_caretaker_TestNode) {
                $testChildNodes = array($node);
            } else {
                $testChildNodes = array();
            }

            $nodeErrors = array();
            $nodeWarnings = array();
            $nodeUndefined = array();
            $nodeAck = array();
            $nodeDue = array();

            $i = 0;
            /** @var tx_caretaker_AbstractNode $testNode */
            foreach ($testChildNodes as $testNode) {
                $testResult = $testNode->getTestResult();
                $instance = $testNode->getInstance();
                if ($testResult->getState() != 0) {
                    $i++;
                    $nodeInfo = array(
                        'num' => $i,
                        'title' => 'title_' . rand(),

                        'node_title' => $testNode->getTitle(),
                        'node_id' => $testNode->getCaretakerNodeId(),

                        'instance_title' => $instance->getTitle(),
                        'instance_id' => $instance->getCaretakerNodeId(),

                        'timestamp' => $testResult->getTimestamp(),
                        'stateinfo' => $testResult->getStateInfo(),
                        'stateinfo_ll' => $testResult->getLocallizedStateInfo(),
                        'message' => $testResult->getLocallizedInfotext(),
                        'message_ll' => $testResult->getLocallizedInfotext(),
                        'state' => $testResult->getState(),
                    );

                    switch ($testResult->getState()) {
                        case tx_caretaker_Constants::state_warning:
                            $nodeWarnings[] = $nodeInfo;
                            break;
                        case tx_caretaker_Constants::state_error:
                            $nodeErrors[] = $nodeInfo;
                            break;
                        case tx_caretaker_Constants::state_undefined:
                            $nodeUndefined[] = $nodeInfo;
                            break;
                        case tx_caretaker_Constants::state_ack:
                            $nodeAck[] = $nodeInfo;
                            break;
                        case tx_caretaker_Constants::state_due:
                            $nodeDue[] = $nodeInfo;
                            break;
                    }
                }
            }

            $content = array();
            $content['nodeProblems'] = array_merge($nodeErrors, $nodeWarnings, $nodeAck, $nodeDue, $nodeUndefined);
            $content['totalCount'] = count($content['nodeProblems']);

            $ajaxObj->setContent($content);
            $ajaxObj->setContentFormat('jsonbody');
        }
    }

    /**
     * Get the contacts for the given node for AJAX
     *
     * @param array $params
     * @param \TYPO3\CMS\Core\Http\AjaxRequestHandler $ajaxObj
     */
    public function ajaxGetNodeContacts($params, &$ajaxObj)
    {
        $node_id = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('node');
        $node_repository = tx_caretaker_NodeRepository::getInstance();
        $node = $node_repository->id2node($node_id, true);

        if ($node_id && $node) {
            $count = 0;
            $contacts = array();
            $nodeContacts = $node->getContacts();

            /** @var tx_caretaker_Contact $nodeContact */
            foreach ($nodeContacts as $nodeContact) {
                $role = $nodeContact->getRole();
                if ($role) {
                    $role_assoc = array(
                        'uid' => $role->getUid(),
                        'id' => $role->getId(),
                        'name' => $role->getTitle(),
                        'description' => $role->getDescription(),
                    );
                } else {
                    $role_assoc = array(
                        'uid' => '',
                        'id' => '',
                        'name' => '',
                        'description' => '',
                    );
                }

                $address = $nodeContact->getAddress();
                if ($address) {
                    $address['email_md5'] = md5($address['email']);
                }

                $contact = array(
                    'num' => $count++,
                    'id' => $node->getCaretakerNodeId() . '_role_' . $role_assoc['uid'] . '_address_' . $address['uid'],

                    'node_title' => $node->getTitle(),
                    'node_type' => $node->getType(),
                    'node_type_ll' => $node->getTypeDescription(),
                    'node_id' => $node->getCaretakerNodeId(),

                    'role' => $role_assoc,
                    'address' => $address,
                );

                foreach ($address as $key => $value) {
                    $contact['address_' . $key] = $value;
                }

                foreach ($role_assoc as $key => $value) {
                    $contact['role_' . $key] = $value;
                }

                $contacts[] = $contact;
            }

            $content = array();
            $content['contacts'] = $contacts;
            $content['totalCount'] = $count;

            $ajaxObj->setContent($content);
            $ajaxObj->setContentFormat('jsonbody');
        }
    }
}
