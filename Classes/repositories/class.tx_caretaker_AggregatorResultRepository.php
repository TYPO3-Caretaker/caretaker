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
 * aggregatorResults. The whole object <-> database
 * communication happens here.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 */
class tx_caretaker_AggregatorResultRepository
{
    /**
     * Reference to the current Instance
     *
     * @var $instance tx_caretaker_TestResultRepository
     */
    private static $instance = null;

    /**
     * Private constructor use getInstance instead
     */
    private function __construct()
    {
    }

    /**
     * Get the Singleton Object
     *
     * @return tx_caretaker_AggregatorResultRepository
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get the latest Testresults for the given Node Object
     *
     * @param tx_caretaker_AbstractNode $node
     * @return tx_caretaker_AggregatorResult
     */
    public function getLatestByNode($node)
    {
        $instance = $node->getInstance();
        if ($instance) {
            $instanceUid = $instance->getUid();
        } else {
            $instanceUid = 0;
        }

        $nodeType = $node->getType();
        $nodeUid = $node->getUid();

        $table = 'tx_caretaker_aggregatorresult';
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $statement = $queryBuilder
            ->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('aggregator_uid',
                    $queryBuilder->createNamedParameter($nodeUid, PDO::PARAM_INT))
            )
            ->andWhere(
                $queryBuilder->expr()->eq('aggregator_type',
                    $queryBuilder->createNamedParameter($nodeType, PDO::PARAM_STR))
            )
            ->andWhere(
                $queryBuilder->expr()->eq('instance_uid',
                    $queryBuilder->createNamedParameter($instanceUid, PDO::PARAM_INT))
            )
            ->orderBy('tstamp', 'DESC')
            ->setMaxResults(1)
            ->execute();
        $row = $statement->fetch();

        if ($row) {
            $result = $this->dbrow2instance($row);

            return $result;
        }
        return new tx_caretaker_AggregatorResult();
    }

    /**
     * Get the ResultRange for the given Aggregator and the timerange
     *
     * @param tx_caretaker_AbstractNode $node
     * @param int $start_timestamp
     * @param int $stop_timestamp
     * @return tx_caretaker_AggregatorResultRange
     */
    public function getRangeByNode($node, $start_timestamp, $stop_timestamp)
    {
        $result_range = new tx_caretaker_AggregatorResultRange($start_timestamp, $stop_timestamp);

        $instance = $node->getInstance();
        if ($instance) {
            $instanceUid = $instance->getUid();
        } else {
            $instanceUid = 0;
        }

        $nodeType = $node->getType();
        $nodeUid = $node->getUid();

        $table = 'tx_caretaker_aggregatorresult';
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $statement = $queryBuilder
            ->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('aggregator_uid',
                    $queryBuilder->createNamedParameter($nodeUid, PDO::PARAM_INT))
            )
            ->andWhere(
                $queryBuilder->expr()->eq('aggregator_type',
                    $queryBuilder->createNamedParameter($nodeType, PDO::PARAM_STR))
            )
            ->andWhere(
                $queryBuilder->expr()->eq('instance_uid',
                    $queryBuilder->createNamedParameter($instanceUid, PDO::PARAM_INT))
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
            $table = 'tx_caretaker_aggregatorresult';
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
            $statement = $queryBuilder
                ->select('*')
                ->from($table)
                ->where(
                    $queryBuilder->expr()->eq('aggregator_uid',
                        $queryBuilder->createNamedParameter($nodeUid, PDO::PARAM_INT))
                )
                ->andWhere(
                    $queryBuilder->expr()->eq('aggregator_type',
                        $queryBuilder->createNamedParameter($nodeType, PDO::PARAM_STR))
                )
                ->andWhere(
                    $queryBuilder->expr()->eq('instance_uid',
                        $queryBuilder->createNamedParameter($instanceUid, PDO::PARAM_INT))
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
                $result_range->addResult($result);
            }
        }

        // add last value if needed
        /** @var tx_caretaker_AggregatorResult $last */
        $last = $result_range->getLast();
        if ($last && $last->getTimestamp() < $stop_timestamp) {
            $real_last = new tx_caretaker_AggregatorResult($stop_timestamp, $last->getState(), $last->getNumUNDEFINED(), $last->getNumOK(), $last->getNumWARNING(), $last->getNumERROR(), $last->getMessage()->getText());
            $result_range->addResult($real_last);
        }

        return $result_range;
    }

    /**
     *
     * @param tx_caretaker_AggregatorNode $node
     * @return int
     */
    public function getResultNumberByNode($node)
    {
        $instance = $node->getInstance();
        if ($instance) {
            $instanceUid = $instance->getUid();
        } else {
            $instanceUid = 0;
        }

        $nodeType = $node->getType();
        $nodeUid = $node->getUid();

        $table = 'tx_caretaker_aggregatorresult';
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $statement = $queryBuilder
            ->select('COUNT(*) AS number')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('aggregator_uid',
                    $queryBuilder->createNamedParameter($nodeUid, PDO::PARAM_INT))
            )
            ->andWhere(
                $queryBuilder->expr()->eq('aggregator_type',
                    $queryBuilder->createNamedParameter($nodeType, PDO::PARAM_STR))
            )
            ->andWhere(
                $queryBuilder->expr()->eq('instance_uid',
                    $queryBuilder->createNamedParameter($instanceUid, PDO::PARAM_INT))
            )
            ->setMaxResults(1)
            ->execute();

        $row = $statement->fetch();

        if ($row) {
            return $row['number'];
        }
        return 0;
    }

    /**
     * @param tx_caretaker_AbstractNode $node
     * @param int $offset
     * @param int $limit
     * @return tx_caretaker_AggregatorResultRange
     */
    public function getResultRangeByNodeAndOffset($node, $offset = 0, $limit = 10)
    {
        $result_range = new tx_caretaker_AggregatorResultRange(null, null);

        $instance = $node->getInstance();
        if ($instance) {
            $instanceUid = $instance->getUid();
        } else {
            $instanceUid = 0;
        }

        $nodeType = $node->getType();
        $nodeUid = $node->getUid();

        $table = 'tx_caretaker_aggregatorresult';
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $statement = $queryBuilder
            ->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('aggregator_uid',
                    $queryBuilder->createNamedParameter($nodeUid, PDO::PARAM_INT))
            )
            ->andWhere(
                $queryBuilder->expr()->eq('aggregator_type',
                    $queryBuilder->createNamedParameter($nodeType, PDO::PARAM_STR))
            )
            ->andWhere(
                $queryBuilder->expr()->eq('instance_uid',
                    $queryBuilder->createNamedParameter($instanceUid, PDO::PARAM_INT))
            )
            ->orderBy('tstamp', 'DESC')
            ->setFirstResult((int) $offset)
            ->setMaxResults((int) $limit)
            ->execute();

        while ($row = $statement->fetch()) {
            $result = $this->dbrow2instance($row);
            $result_range->addResult($result);
        }

        return $result_range;
    }

    /**
     * Save Aggregator Result to the DB
     *
     * @param tx_caretaker_AggregatorNode $node
     * @param tx_caretaker_AggregatorResult $aggregator_result
     * @return int UID of the new DB result Record
     */
    public function addNodeResult(tx_caretaker_AggregatorNode $node, tx_caretaker_AggregatorResult $aggregator_result)
    {
        //add an undefined row to the testresult column
        $instance = $node->getInstance();
        if ($instance) {
            $instanceUid = $instance->getUid();
        } else {
            $instanceUid = 0;
        }

        $values = array(
            'aggregator_uid' => $node->getUid(),
            'aggregator_type' => $node->getType(),
            'instance_uid' => $instanceUid,

            'result_status' => $aggregator_result->getState(),
            'tstamp' => $aggregator_result->getTimestamp(),
            'result_num_undefined' => $aggregator_result->getNumUNDEFINED(),
            'result_num_ok' => $aggregator_result->getNumOK(),
            'result_num_warnig' => $aggregator_result->getNumWARNING(),
            'result_num_error' => $aggregator_result->getNumERROR(),
            'result_msg' => $aggregator_result->getMessage()->getText(),
            'result_values' => serialize($aggregator_result->getMessage()->getValues()),
            'result_submessages' => serialize($aggregator_result->getSubMessages()),
        );

        $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_caretaker_aggregatorresult', $values);

        return $GLOBALS['TYPO3_DB']->sql_insert_id();
    }

    /**
     * Convert DB-Row to Aggregator Node Result
     *
     * @param array $row DB Row
     * @return tx_caretaker_AggregatorResult
     */
    private function dbrow2instance($row)
    {
        $message = new tx_caretaker_ResultMessage($row['result_msg'], unserialize($row['result_values']));
        $submessages = ($row['result_submessages']) ? unserialize($row['result_submessages']) : array();
        $instance = new tx_caretaker_AggregatorResult(
            $row['tstamp'],
            $row['result_status'],
            $row['result_num_undefined'],
            $row['result_num_ok'],
            $row['result_num_warnig'],
            $row['result_num_error'],
            $message,
            $submessages
        );

        return $instance;
    }
}
