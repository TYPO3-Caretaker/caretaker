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

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
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
 * Repository to handle the storing and reconstruction of all
 * testResults. The whole object <-> database
 * communication happens here.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 */
class tx_caretaker_TestResultRepository
{
    /**
     * Reference to the current Instance
     *
     * @var $instance tx_caretaker_TestResultRepository
     */
    private static $instance = null;

    /**
     * The time in seconds to search for the last node result
     *
     * @var int
     */
    private $lastTestResultScanRange = 0;

    /**
     * Private constructor use getInstance instead
     */
    private function __construct()
    {
        $confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
        $this->lastTestResultScanRange = (int)$confArray['lastTestResultScanRange'];
    }

    /**
     * Get the Singleton Object
     *
     * @return tx_caretaker_TestResultRepository
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get the latest Testresult for the given Instance and Test
     *
     * @param tx_caretaker_TestNode $testNode
     * @return tx_caretaker_TestResult
     */
    public function getLatestByNode(tx_caretaker_TestNode $testNode)
    {
        $testUID = $testNode->getUid();
        $instanceUID = $testNode->getInstance()->getUid();

        $table = 'tx_caretaker_lasttestresult';
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $statement = $queryBuilder
            ->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('test_uid',
                    $queryBuilder->createNamedParameter($testUID, PDO::PARAM_INT))
            )
            ->andWhere(
                $queryBuilder->expr()->eq('instance_uid',
                    $queryBuilder->createNamedParameter($instanceUID, PDO::PARAM_INT))
            )
            ->setMaxResults(1)
            ->execute();
        $row = $statement->fetch();

        if ($row) {
            return $this->dbrow2instance($row);
        }
        return new tx_caretaker_TestResult();
    }

    /**
     * Get the latest Testresult for the given Instance and Test
     *
     * @param tx_caretaker_AbstractNode $testNode
     * @param $currentResult
     * @return tx_caretaker_TestResult
     */
    public function getPreviousDifferingResult($testNode, $currentResult)
    {
        $row = null;
        if ($testNode instanceof tx_caretaker_TestNode) {
            $testUID = $testNode->getUid();
            $instanceUID = $testNode->getInstance()->getUid();

            $table = 'tx_caretaker_testresult';
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
            $statement = $queryBuilder
                ->select('*')
                ->from($table)
                ->where(
                    $queryBuilder->expr()->eq('test_uid',
                        $queryBuilder->createNamedParameter($testUID, PDO::PARAM_INT))
                )
                ->andWhere(
                    $queryBuilder->expr()->eq('instance_uid',
                        $queryBuilder->createNamedParameter($instanceUID, PDO::PARAM_INT))
                )
                ->andWhere(
                    $queryBuilder->expr()->neq('result_status',
                        $queryBuilder->createNamedParameter($currentResult->getState(), PDO::PARAM_INT))
                )
                ->andWhere(
                    $queryBuilder->expr()->lt('tstamp',
                        $queryBuilder->createNamedParameter($currentResult->getTimestamp(), PDO::PARAM_INT))
                )
                ->orderBy('tstamp', 'DESC')
                ->addOrderBy('uid', 'DESC')
                ->setMaxResults(1)
                ->execute();
            $row = $statement->fetch();
        }

        if ($row) {
            $result = $this->dbrow2instance($row);

            return $result;
        }
        return new tx_caretaker_TestResult();
    }

    /**
     * Return the Number of available TestResults
     *
     * @param  tx_caretaker_TestNode $testNode
     * @return int
     */
    public function getResultNumberByNode(tx_caretaker_TestNode $testNode)
    {
        $testUID = $testNode->getUid();
        $instanceUID = $testNode->getInstance()->getUid();

        $table = 'tx_caretaker_testresult';
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $statement = $queryBuilder
            ->select('COUNT(*) AS number')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('test_uid',
                    $queryBuilder->createNamedParameter($testUID, PDO::PARAM_INT))
            )
            ->andWhere(
                $queryBuilder->expr()->eq('instance_uid',
                    $queryBuilder->createNamedParameter($instanceUID, PDO::PARAM_INT))
            )
            ->setMaxResults(1)
            ->execute();
        $row = $statement->fetch();

        if ($row) {
            return (int)$row['number'];
        }
        return 0;
    }

    /**
     * Get a List of Testresults defined by Offset and Limit
     *
     * @param tx_caretaker_TestNode $testNode
     * @param int $offset
     * @param int $limit
     * @return tx_caretaker_TestResultRange
     */
    public function getResultRangeByNodeAndOffset(tx_caretaker_TestNode $testNode, $offset = 0, $limit = 10)
    {
        $testUID = $testNode->getUid();
        $instanceUID = $testNode->getInstance()->getUid();

        $result_range = new tx_caretaker_TestResultRange(null, null);

        $table = 'tx_caretaker_testresult';
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $statement = $queryBuilder
            ->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('test_uid',
                    $queryBuilder->createNamedParameter($testUID, PDO::PARAM_INT))
            )
            ->andWhere(
                $queryBuilder->expr()->eq('instance_uid',
                    $queryBuilder->createNamedParameter($instanceUID, PDO::PARAM_INT))
            )
            ->orderBy('tstamp', 'DESC')
            ->setFirstResult((int)$offset)
            ->setMaxResults((int)$limit)
            ->execute();

        while ($row = $statement->fetch()) {
            $result = $this->dbrow2instance($row);
            $result_range->addResult($result);
        }

        return $result_range;
    }

    /**
     * Get the ResultRange for the given Instance Test and the timerange
     *
     * @param tx_caretaker_TestNode $testNode
     * @param int $start_timestamp
     * @param int $stop_timestamp
     * @param bool $graph By default the result range is created for the graph, so the last result is added again at the end
     * @return tx_caretaker_TestResultRange
     */
    public function getRangeByNode(tx_caretaker_TestNode $testNode, $start_timestamp, $stop_timestamp, $graph = true)
    {
        $testUID = $testNode->getUid();
        $instanceUID = $testNode->getInstance()->getUid();

        $result_range = new tx_caretaker_TestResultRange($start_timestamp, $stop_timestamp);

        $table = 'tx_caretaker_testresult';
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $statement = $queryBuilder
            ->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('test_uid',
                    $queryBuilder->createNamedParameter($testUID, PDO::PARAM_INT))
            )
            ->andWhere(
                $queryBuilder->expr()->eq('instance_uid',
                    $queryBuilder->createNamedParameter($instanceUID, PDO::PARAM_INT))
            )
            ->andWhere(
                $queryBuilder->expr()->gte('tstamp',
                    $queryBuilder->createNamedParameter($start_timestamp, PDO::PARAM_INT))
            )
            ->andWhere(
                $queryBuilder->expr()->lte('tstamp',
                    $queryBuilder->createNamedParameter($stop_timestamp, PDO::PARAM_INT))
            )
            ->orderBy('tstamp', 'ASC')
            ->execute();

        while ($row = $statement->fetch()) {
            $result = $this->dbrow2instance($row);
            $result_range->addResult($result);
        }

        // add first value if needed
        $first = $result_range->getFirst();
        if (!$first || ($first && $first->getTimestamp() > $start_timestamp)) {
            $table = 'tx_caretaker_testresult';
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
            $statement = $queryBuilder
                ->select('*')
                ->from($table)
                ->where(
                    $queryBuilder->expr()->eq('test_uid',
                        $queryBuilder->createNamedParameter($testUID, PDO::PARAM_INT))
                )
                ->andWhere(
                    $queryBuilder->expr()->eq('instance_uid',
                        $queryBuilder->createNamedParameter($instanceUID, PDO::PARAM_INT))
                )
                ->andWhere(
                    $queryBuilder->expr()->lt('tstamp',
                        $queryBuilder->createNamedParameter($start_timestamp, PDO::PARAM_INT))
                )
                ->orderBy('tstamp', 'DESC')
                ->setMaxResults(1)
                ->execute();
            if ($row = $statement->fetch()) {
                $row['tstamp'] = $start_timestamp;
                $result = $this->dbrow2instance($row);
                $result_range->addResult($result, 'first');
            }
        }

        // add last value if needed
        $last = $result_range->getLast();
        if ($last && $last->getTimestamp() < $stop_timestamp) {
            if ($graph) {
                $real_last = new tx_caretaker_TestResult($stop_timestamp, $last->getState(), $last->getValue(), $last->getMessage()->getText(), $last->getSubMessages());
                $result_range->addResult($real_last);
            }
        }

        return $result_range;
    }

    /**
     * Convert DB-Row to Test Node Result
     *
     * @param array $row
     * @return tx_caretaker_TestResult
     */
    private function dbrow2instance($row)
    {
        $message = new tx_caretaker_ResultMessage($row['result_msg'], unserialize($row['result_values']));
        $submessages = ($row['result_submessages']) ? unserialize($row['result_submessages']) : array();
        $instance = new tx_caretaker_TestResult(
            $row['tstamp'],
            $row['result_status'],
            $row['result_value'],
            $message,
            $submessages
        );

        return $instance;
    }

    /**
     * Save the Testresult for the given TestNode
     *
     * @param tx_caretaker_TestNode $test
     * @param tx_caretaker_TestResult $testResult
     */
    public function saveTestResultForNode(tx_caretaker_TestNode $test, $testResult)
    {
        $values = array(
            'test_uid' => $test->getUid(),
            'instance_uid' => $test->getInstance()->getUid(),
            'tstamp' => $testResult->getTimestamp(),
            'result_status' => $testResult->getState(),
            'result_value' => $testResult->getValue(),
            'result_msg' => $testResult->getMessage()->getText(),
            'result_values' => serialize($testResult->getMessage()->getValues()),
            'result_submessages' => serialize($testResult->getSubMessages()),
        );

        // store log of results
        $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_caretaker_testresult', $values);

        // store last results for fast access
        $table = 'tx_caretaker_lasttestresult';
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $statement = $queryBuilder
            ->select('uid')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('test_uid',
                    $queryBuilder->createNamedParameter($test->getUid(), PDO::PARAM_INT))
            )
            ->andWhere(
                $queryBuilder->expr()->eq('instance_uid',
                    $queryBuilder->createNamedParameter($test->getInstance()->getUid(), PDO::PARAM_INT))
            )
            ->setMaxResults(1)
            ->execute();
        if ($row = $statement->fetch()) {
            $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_caretaker_lasttestresult', 'uid = ' . $row['uid'], $values);
        } else {
            $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_caretaker_lasttestresult', $values);
        }
    }
}
