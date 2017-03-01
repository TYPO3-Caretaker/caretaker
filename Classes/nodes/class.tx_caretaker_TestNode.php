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
 * Caretaker-node that represents the concrete caretaker-tests which
 * are assigned to instances or instancegroups. Testnodes are the leafs of the
 * caretaker node-tree.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 */
class tx_caretaker_TestNode extends tx_caretaker_AbstractNode
{
    /**
     * Test Service Type
     *
     * @var string
     */
    protected $testServiceType;

    /**
     * Configuration of the test
     *
     * @var bool|array
     */
    protected $testServiceConfiguration = false;

    /**
     * Reference to the test service
     *
     * @var tx_caretaker_TestServiceInterface
     */
    protected $testService = null;

    /**
     * Interval of Tests in Seconds
     *
     * @var int
     */
    protected $testInterval = false;

    /**
     * Retry the test n times after failure or warning
     *
     * @var int
     */
    protected $testRetry = 0;

    /**
     * Set the due mode
     *
     * @var int
     */
    protected $testDue = 0;

    /**
     * The test shall be executed only after this hour
     *
     * @var int
     */
    protected $startHour = false;

    /**
     * The test shall be executed only before this hour
     *
     * @var int
     */
    protected $stopHour = false;

    /**
     * @var tx_caretaker_TestServiceRunner
     */
    protected $testServiceRunner = null;

    /**
     * @var array
     */
    protected $rolesIds;

    /**
     * Constructor
     *
     * @param int $uid
     * @param string $title
     * @param tx_caretaker_AbstractNode $parentNode
     * @param string $serviceType
     * @param string $serviceConfiguration
     * @param int $interval
     * @param int $retry
     * @param int $due
     * @param bool|int $startHour
     * @param bool|int $stopHour
     * @param bool $hidden
     * @param null|mixed $rolesIds
     */
    public function __construct($uid, $title, $parentNode, $serviceType, $serviceConfiguration, $interval = 86400, $retry = 0, $due = 0, $startHour = false, $stopHour = false, $hidden = false, $rolesIds = null)
    {
        // Overwrite default test configuration
        $configurationOverlay = $parentNode->getTestConfigurationOverlayForTestUid($uid);
        if ($configurationOverlay) {
            $serviceConfiguration = $configurationOverlay;
            if ($serviceConfiguration['hidden']) {
                $hidden = true;
            }
        }

        parent::__construct($uid, $title, $parentNode, tx_caretaker_Constants::table_Tests, tx_caretaker_Constants::nodeType_Test, $hidden);

        $this->testServiceType = $serviceType;
        $this->testServiceConfiguration = $serviceConfiguration;
        $this->testInterval = $interval;
        $this->testRetry = $retry;
        $this->testDue = $due;
        $this->startHour = $startHour;
        $this->stopHour = $stopHour;
        $this->rolesIds = $rolesIds ? explode(',', $rolesIds) : array();
    }

    /**
     * @throws Exception
     * @return tx_caretaker_TestServiceInterface
     */
    public function getTestService()
    {
        if ($this->testService === null) {
            if ($this->testServiceType) {
                $info = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::findService('caretaker_test_service', $this->testServiceType);
                if ($info && $info['className']) {
                    if (class_exists($info['className'])) {
                        $this->testService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($info['className']);
                        if ($this->testService) {
                            $this->testService->setInstance($this->getInstance());
                            $this->testService->setConfiguration($this->testServiceConfiguration);
                        } else {
                            throw new Exception('testservice class ' . $info['className'] . ' could not be instantiated');
                        }
                    } else {
                        throw new Exception('testservice ' . $this->testServiceType . ' class ' . $info['className'] . ' not found');
                    }
                } else {
                    throw new Exception('caretaker testservice ' . $this->testServiceType . ' not found');
                }
            }
        }

        return $this->testService;
    }

    /**
     * Get the caretaker node id of this node
     *
     * @return string
     */
    public function getCaretakerNodeId()
    {
        $instance = $this->getInstance();

        return 'instance_' . $instance->getUid() . '_test_' . $this->getUid();
    }

    /**
     * Get the description of the Testservice
     *
     * @return string
     */
    public function getTypeDescription()
    {
        if ($this->testServiceType) {
            return $this->getTestService()->getTypeDescription();
        }
    }

    /**
     * Get the description of the Testsevice
     *
     * @return string
     */
    public function getConfigurationInfo()
    {
        if ($this->testServiceType) {
            $configurationInfo = $this->getTestService()->getConfigurationInfo();
            if (isset($this->testServiceConfiguration['overwritten_in'])
                && is_array($this->testServiceConfiguration['overwritten_in'])
            ) {
                $configurationInfo .= ' (overwritten in ' .
                    '<span title=" ' .
                    $this->testServiceConfiguration['overwritten_in']['id'] .
                    '">' .
                    $this->testServiceConfiguration['overwritten_in']['title'] .
                    '</span>)';
            }

            return $configurationInfo;
        }
    }

    /**
     * @return string
     */
    public function getHiddenInfo()
    {
        $hiddenInfo = parent::getHiddenInfo();
        if ($this->testServiceType) {
            if (isset($this->testServiceConfiguration['overwritten_in'])
                && is_array($this->testServiceConfiguration['overwritten_in'])
                && $this->testServiceConfiguration['hidden']
            ) {
                $hiddenInfo .= ' (hidden in ' .
                    '<span title=" ' .
                    $this->testServiceConfiguration['overwritten_in']['id'] .
                    '">' .
                    $this->testServiceConfiguration['overwritten_in']['title'] .
                    '</span>)';
            }
        }

        return $hiddenInfo;
    }

    /**
     * Get the Test Interval
     *
     * @return int|bool
     */
    public function getInterval()
    {
        return $this->testInterval;
    }

    /**
     * Get the test start hour
     *
     * @return int|bool
     */
    public function getStartHour()
    {
        return $this->startHour;
    }

    /**
     * Get the test stop hour
     *
     * @return int|bool
     */
    public function getStopHour()
    {
        return $this->stopHour;
    }

    /**
     * Set the testnode into acknowledged State
     *
     * @return tx_caretaker_TestResult
     */
    public function setModeAck()
    {
        $info = array(
            'username' => 'unknown',
            'realName' => 'unknown',
            'email' => 'unknown',
        );
        if (TYPO3_MODE == 'BE') {
            $info['username'] = $GLOBALS['BE_USER']->user['username'];
            $info['realName'] = $GLOBALS['BE_USER']->user['realName'];
            $info['email'] = $GLOBALS['BE_USER']->user['email'];
        }

        $resultRepository = tx_caretaker_TestResultRepository::getInstance();
        $latestTestResult = $resultRepository->getLatestByNode($this);

        $message = new tx_caretaker_ResultMessage('LLL:EXT:caretaker/locallang_fe.xml:message_ack', $info);
        $result = tx_caretaker_TestResult::create(tx_caretaker_Constants::state_ack, 0, $message);
        $resultRepository->saveTestResultForNode($this, $result);

        $this->notify('updatedTestResult', $result, $latestTestResult);

        return $result;
    }

    /**
     * End the wip state by running a forced update
     *
     * @return tx_caretaker_TestResult
     */
    public function setModeDue()
    {
        $info = array(
            'username' => 'unknown',
            'realName' => 'unknown',
            'email' => 'unknown',
        );
        if (TYPO3_MODE == 'BE') {
            $info['username'] = $GLOBALS['BE_USER']->user['username'];
            $info['realName'] = $GLOBALS['BE_USER']->user['realName'];
            $info['email'] = $GLOBALS['BE_USER']->user['email'];
        }

        $resultRepository = tx_caretaker_TestResultRepository::getInstance();
        $latestTestResult = $resultRepository->getLatestByNode($this);

        $message = new tx_caretaker_ResultMessage('LLL:EXT:caretaker/locallang_fe.xml:message_due', $info);
        $result = tx_caretaker_TestResult::create(tx_caretaker_Constants::state_due, 0, $message);
        $resultRepository->saveTestResultForNode($this, $result);

        $this->notify('updatedTestResult', $result, $latestTestResult);

        return $result;
    }

    /**
     * Update TestResult and store in DB. If the Test is not due the result is fetched from the cache.
     *
     * If force is not set the execution time and exclude hours are taken in account.
     *
     * @param array $options Options for running this test
     * @return tx_caretaker_NodeResult
     */
    public function updateTestResult($options = array())
    {
        if ($this->getHidden()) {
            $result = tx_caretaker_TestResult::undefined('Node is disabled');
            $this->notify('disabledTestResult', $result);

            return $result;
        }

        return $this->getTestServiceRunner()->runTestService($this->getTestService(), $this, $options);
    }

    /**
     * @return tx_caretaker_TestServiceRunner
     */
    protected function getTestServiceRunner()
    {
        if ($this->testServiceRunner === null) {
            if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['caretaker']['TestServiceRunner'])) {
                $testServiceRunnerClassName = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['caretaker']['TestServiceRunner'];
            } else {
                $testServiceRunnerClassName = 'tx_caretaker_TestServiceRunner';
            }
            $this->testServiceRunner = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($testServiceRunnerClassName);
        }

        return $this->testServiceRunner;
    }

    /**
     * @param tx_caretaker_TestServiceRunner $testServiceRunner
     * @return void
     */
    public function setTestServiceRunner($testServiceRunner)
    {
        $this->testServiceRunner = $testServiceRunner;
    }

    /**
     * Get the all tests which can be found below this node
     *
     * @return array
     * @deprecated This should be only necessary for aggregator nodes
     */
    public function getTestNodes()
    {
        return array($this);
    }

    /**
     * Get the Value Description for this test
     *
     * @see caretaker/trunk/Classes/nodes/tx_caretaker_AbstractNode#getValueDescription()
     */
    public function getValueDescription()
    {
        $test_service = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstanceService('caretaker_test_service', $this->testServiceType);
        if ($test_service) {
            return $test_service->getValueDescription();
        }
        return 'unknown service ' . $this->testServiceType;
    }

    /**
     * Get the current Test Result from Cache
     *
     * @see caretaker/trunk/Classes/nodes/tx_caretaker_AbstractNode#getTestResult()
     * @return tx_caretaker_TestResult
     */
    public function getTestResult()
    {
        if ($this->getHidden()) {
            $result = tx_caretaker_TestResult::undefined('Node is disabled');

            return $result;
        }

        $test_result_repository = tx_caretaker_TestResultRepository::getInstance();
        $result = $test_result_repository->getLatestByNode($this);

        return $result;
    }

    /**
     * Get the number of available Test Results
     *
     * @return int
     */
    public function getTestResultNumber()
    {
        $test_result_repository = tx_caretaker_TestResultRepository::getInstance();
        $resultNumber = $test_result_repository->getResultNumberByNode($this);

        return $resultNumber;
    }

    /**
     * Get the TestResultRange for the given time range
     *
     * @see caretaker/trunk/Classes/nodes/tx_caretaker_AbstractNode#getTestResultRange()
     * @param int $start_timestamp
     * @param int $stop_timestamp
     * @param bool $graph True by default. Used in the result range repository the specify the handling of the last result. For more information see tx_caretaker_testResultRepository.
     * @return tx_caretaker_TestResultRange
     */
    public function getTestResultRange($start_timestamp, $stop_timestamp, $graph = true)
    {
        $test_result_repository = tx_caretaker_TestResultRepository::getInstance();
        $resultRange = $test_result_repository->getRangeByNode($this, $start_timestamp, $stop_timestamp, $graph);

        return $resultRange;
    }

    /**
     * Get the TestResultRange for the Offset and Limit
     *
     * @see caretaker/trunk/Classes/nodes/tx_caretaker_AbstractNode#getTestResultRange()
     * @param int $offset
     * @param int $limit
     * @return tx_caretaker_TestResultRange
     */
    public function getTestResultRangeByOffset($offset = 0, $limit = 10)
    {
        $test_result_repository = tx_caretaker_TestResultRepository::getInstance();
        $resultRange = $test_result_repository->getResultRangeByNodeAndOffset($this, $offset, $limit);

        return $resultRange;
    }

    /**
     * @return int
     */
    public function getTestInterval()
    {
        return $this->testInterval;
    }

    /**
     * @return int
     */
    public function getTestRetry()
    {
        return $this->testRetry;
    }

    /**
     * @return int
     */
    public function getTestDue()
    {
        return $this->testDue;
    }

    /**
     * @return array
     */
    public function getRolesIds()
    {
        return $this->rolesIds;
    }
}
