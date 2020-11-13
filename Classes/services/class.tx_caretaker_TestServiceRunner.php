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
 * $Id: class.tx_caretaker_TestServiceBase.php 43817 2011-02-18 11:29:43Z etobi.de $
 */

/**
 * Base strategy for running test services for a test node.
 * A custom implementation could be used to clus
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 */
class tx_caretaker_TestServiceRunner extends \TYPO3\CMS\Core\Service\AbstractService
{
    /**
     * Run a test service for the given test node
     *
     * @param tx_caretaker_TestServiceInterface $testService
     * @param tx_caretaker_TestNode $node
     * @param array $options
     * @return tx_caretaker_NodeResult
     */
    public function runTestService($testService, $node, $options)
    {
        $testResultRepository = tx_caretaker_TestResultRepository::getInstance();
        $latestTestResult = $testResultRepository->getLatestByNode($node);

        $returnLatestResult = $this->shouldReturnLatestResult($node, $latestTestResult, $options);

        if ($returnLatestResult) {
            $node->notify('cachedTestResult', $latestTestResult);

            return $latestTestResult;
        }
        return $this->executeTestServiceRun($testService, $node, $latestTestResult, $options);
    }

    /**
     *
     * @param tx_caretaker_TestNode $node
     * @param tx_caretaker_NodeResult $latestTestResult
     * @param array $options
     * @return bool
     */
    protected function shouldReturnLatestResult($node, $latestTestResult, $options)
    {
        $forceUpdate = isset($options['forceUpdate']) && $options['forceUpdate'] === true;
        $returnLatestResult = false;
        if (!$forceUpdate) {
            if ($latestTestResult) {
                // test is not in due state so retry
                switch ($latestTestResult->getState()) {
                    case tx_caretaker_Constants::state_due:
                        $returnLatestResult = false;
                        break;
                    case tx_caretaker_Constants::state_ack:
                        $returnLatestResult = true;
                        break;
                    case tx_caretaker_Constants::state_ok:
                        if ($latestTestResult->getTimestamp() > (time() - $node->getTestInterval())) {
                            $returnLatestResult = true;
                        }
                        break;
                    case tx_caretaker_Constants::state_undefined:
                    case tx_caretaker_Constants::state_warning:
                    case tx_caretaker_Constants::state_error:
                        // if due mode is 1 than retry
                        if ($node->getTestDue() == 1) {
                            $returnLatestResult = false;
                        } elseif ($latestTestResult->getTimestamp() > (time() - $node->getTestInterval())) {
                            $returnLatestResult = true;
                        }
                        break;
                }
            }

            // test should not run this hour
            if (!$returnLatestResult && ($node->getStartHour() > 0 || $node->getStopHour() > 0)) {
                $localTime = localtime(time(), true);
                $localHour = $localTime['tm_hour'];
                if ($localHour < $node->getStartHour() || $localHour >= $node->getStopHour()) {
                    $returnLatestResult = true;
                }
            }
        }

        return $returnLatestResult;
    }

    /**
     * @param tx_caretaker_TestServiceInterface $testService
     * @param tx_caretaker_TestNode $node
     * @param tx_caretaker_NodeResult $latestTestResult
     * @param array $options
     * @return tx_caretaker_NodeResult
     */
    protected function executeTestServiceRun($testService, $node, $latestTestResult, $options)
    {
        // check whether the test can be executed
        if ($testService && $testService->isExecutable()) {
            try {
                $result = $testService->runTest();
            } catch (Exception $e) {
                throw new RuntimeException(
                    'Execution of Caretaker TestService failed with: ' . $e->getMessage(), 1605201669);
            }

            // retry if not ok and retrying is enabled
            if ($result->getState() != 0 && $node->getTestRetry() > 0) {
                $round = 0;
                while ($round < $node->getTestRetry() && $result->getState() != 0) {
                    // TODO make sleep time between retry configurable
                    sleep(1);
                    try {
                        $result = $testService->runTest();
                    } catch (Exception $e) {
                        throw new RuntimeException(
                            'Execution of Caretaker TestService failed with: ' . $e->getMessage(),
                            1605201757);
                    }
                    $round++;
                }
                $result->addSubMessage(new tx_caretaker_ResultMessage('LLL:EXT:caretaker/locallang_fe.xml:retry_info', array('number' => $round)));
            }

            // save to repository after reading the previous result
            $resultRepository = tx_caretaker_TestResultRepository::getInstance();
            $resultRepository->saveTestResultForNode($node, $result);

            // trigger notification
            $node->notify('updatedTestResult', $result, $latestTestResult);

            return $result;
        }
        $result = tx_caretaker_TestResult::undefined();
        $result->addSubMessage(new tx_caretaker_ResultMessage('test service was not executable this time so the cached result is used'));
        $node->notify('cachedTestResult', $result, $latestTestResult);

        return $latestTestResult;
    }
}
