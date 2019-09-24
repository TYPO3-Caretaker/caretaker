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

use TYPO3\CMS\Core\Utility\GeneralUtility;

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
 * The caretaker cli script.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 */
class tx_caretaker_Cli extends \TYPO3\CMS\Core\Controller\CommandLineController
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Running parent class constructor
        parent::__construct();

        // Setting help texts:
        $this->cli_help['name'] = 'Caretaker CLI-Testrunner';

        $this->cli_help['synopsis'] = 'update|get|wip|update-typo3-latest-version-list|help ###OPTIONS###';

        $this->cli_help['description'] = 'Class with basic functionality for CLI scripts';
        $this->cli_help['examples'] = '../cli_dispatch.phpsh caretaker update --root';
        $this->cli_help['author'] = 'Martin Ficzel, (c) 2008-2010';

        $this->cli_options[] = array('--root', 'update all beginning with Root Node');
        $this->cli_options[] = array('-R', 'Same as --root');

        $this->cli_options[] = array('--node', 'update all beginning with this NodeID');
        $this->cli_options[] = array('-N', 'Same as --node');

        $this->cli_options[] = array('-f', 'force Refresh of testResults');
        $this->cli_options[] = array('-r', 'Return status code');

        $this->cli_options[] = array('--options', 'Generic options for running tests, e.g. "timeout=10 reporting=off"');
        $this->cli_options[] = array('-o', 'Same as --options');
    }

    /**
     * @param array $argv
     */
    public function cli_main($argv)
    {
        $task = (string)$this->cli_args['_DEFAULT'][1];

        if (!$task) {
            $this->cli_validateArgs();
            $this->cli_help();
            exit;
        }

        if ($task == 'update' || $task == 'get' || $task == 'ack' || $task == 'due') {
            $force = (bool)$this->readArgument('--force', '-f');
            $return_status = (bool)$this->readArgument('-r');
            $options = array('forceUpdate' => $force);
            $options = array_merge($options, $this->parseOptions($this->readArgument('--options', '-o')));
            $node = false;

            $node_repository = tx_caretaker_NodeRepository::getInstance();
            $nodeId = $this->readArgument('--node', '-N');
            if ((bool)$this->readArgument('--root', '-R')) {
                $node = $node_repository->getRootNode();
            } elseif ($nodeId) {
                $node = $node_repository->id2node($nodeId);
            }

            if ($node) {
                $this->cli_echo('node ' . $node->getCaretakerNodeId() . chr(10));

                $result = false;

                if ($task == 'update' || $task == 'ack' || $task == 'due') {
                    try {
                        if (class_exists('TYPO3\CMS\Core\Locking\LockFactory')) {
                            $lockObj = GeneralUtility::makeInstance('TYPO3\CMS\Core\Locking\LockFactory')->createLocker('tx_caretaker_update_' . $node->getCaretakerNodeId());
                        } else {
                            $lockObj = GeneralUtility::makeInstance('TYPO3\CMS\Core\Locking\Locker', 'tx_caretaker_update_' . $node->getCaretakerNodeId(), $GLOBALS['TYPO3_CONF_VARS']['SYS']['lockingMode']);
                        }
                        $lockIsAquired = $lockObj->acquire();
                    } catch (Exception $e) {
                        $this->cli_echo('lock ' . 'tx_caretaker_update_' . $node->getCaretakerNodeId() . ' could not be aquired!' . chr(10) . $e->getMessage());
                        exit;
                    }

                    if ($lockIsAquired) {
                        if ($task == 'update') {
                            $result = $node->updateTestResult($options);
                        } elseif ($task == 'ack' && is_a($node, 'tx_caretaker_TestNode')) {
                            $result = $node->setModeAck();
                        } elseif ($task == 'due' && is_a($node, 'tx_caretaker_TestNode')) {
                            $result = $node->setModeDue();
                        }
                        $lockObj->release();
                    } else {
                        $result = false;
                        $this->cli_echo('node ' . $node->getCaretakerNodeId() . ' is locked because of other running update processes!' . chr(10));
                        exit;
                    }
                }

                if ($task == 'get') {
                    $result = $node->getTestResult();
                    $this->cli_echo($node->getTitle() . ' [' . $node->getCaretakerNodeId() . ']' . chr(10));
                    $this->cli_echo($result->getLocallizedStateInfo() . ' [' . $node->getCaretakerNodeId() . ']' . chr(10));
                }

                // send aggregated notifications
                $notificationServices = tx_caretaker_ServiceHelper::getAllCaretakerNotificationServices();
                /** @var tx_caretaker_NotificationBaseExitPoint $notificationService */
                foreach ($notificationServices as $notificationService) {
                    $notificationService->sendNotifications();
                }

                if ($return_status) {
                    exit((int)$result->getState());
                }
                exit;
            }
            $this->cli_echo('Node not found or inactive' . chr(10));
            exit;
        } elseif ($task == 'update-typo3-latest-version-list') {
            $result = tx_caretaker_LatestVersionsHelper::updateLatestTypo3VersionRegistry();
            $this->cli_echo('TYPO3 latest version list update result: ' . $result . chr(10));
            $versions = GeneralUtility::makeInstance('TYPO3\CMS\Core\Registry')->get('tx_caretaker', 'TYPO3versions');
            foreach ($versions as $key => $version) {
                $this->cli_echo($key . ' => ' . $version . chr(10));
            }
        }

        if ($task == 'help') {
            $this->cli_validateArgs();
            $this->cli_help();
            exit;
        }
    }

    /**
     * Get a specific CLI Argument
     *
     * @param string $name
     * @param bool|string $alt_name
     * @return string
     */
    protected function readArgument($name, $alt_name = false)
    {
        if ($name && isset($this->cli_args[$name])) {
            if ($this->cli_args[$name][0]) {
                return $this->cli_args[$name][0];
            }
            return true;
        } elseif ($alt_name) {
            return $this->readArgument($alt_name);
        }
        return false;
    }

    /**
     * Parse additional options (e.g. myvalue=foo) from the
     * additional options argument. Multiple options should be
     * separated by spaces.
     *
     * @param string $optionsString
     * @return array Option values indexed by key
     */
    protected function parseOptions($optionsString)
    {
        $options = array();
        $optionParts = GeneralUtility::trimExplode(' ', $optionsString);
        foreach ($optionParts as $optionPart) {
            list($optionKey, $optionValue) = GeneralUtility::trimExplode('=', $optionPart, 2);
            $options[$optionKey] = $optionValue;
        }

        return $options;
    }
}

// Call the functionality
if (!defined('TYPO3_cliMode')) {
    die('You cannot run this script directly!');
}

$sobe = GeneralUtility::makeInstance('tx_caretaker_Cli');
$sobe->cli_main($_SERVER['argv']);
