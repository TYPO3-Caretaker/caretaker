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
 * Repository to handle the storing and reconstruction of node contacts
 * aggregatorResults. The whole object <-> database
 * communication happens here.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 */
class tx_caretaker_ContactRepository
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
     * @return tx_caretaker_ContactRepository
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get Role Object for given Uid
     *
     * @param <type> $uid
     * @return bool|tx_caretaker_ContactRole
     */
    public function getContactRoleByUid($uid)
    {
        $table = tx_caretaker_Constants::table_Roles;
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $statement = $queryBuilder
            ->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('uid',
                    $queryBuilder->createNamedParameter(intval($uid), PDO::PARAM_INT))
            )
            ->andWhere($queryBuilder->expr()->eq('hidden', 0))
            ->andWhere($queryBuilder->expr()->eq('deleted', 0))
            ->execute();
        if ($row = $statement->fetch()) {
            return $this->dbrow2contact_role($row);
        }
        return false;
    }

    /**
     * Get Role Object for given String
     *
     * @param string $id
     * @return bool|tx_caretaker_ContactRole
     */
    public function getContactRoleById($id)
    {
        $table = tx_caretaker_Constants::table_Roles;
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $statement = $queryBuilder
            ->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('id',
                    $queryBuilder->createNamedParameter($id, PDO::PARAM_INT))
            )
            ->andWhere($queryBuilder->expr()->eq('hidden', 0))
            ->andWhere($queryBuilder->expr()->eq('deleted', 0))
            ->execute();
        if ($row = $statement->fetch()) {
            return $this->dbrow2contact_role($row);
        }
        return false;
    }

    /**
     * Convert dbrow to ContactRole Object
     *
     * @param array $dbrow
     * @return tx_caretaker_ContactRole
     */
    private function dbrow2contact_role($dbrow)
    {
        $role = new tx_caretaker_ContactRole($dbrow['uid'], $dbrow['id'], $dbrow['name'], $dbrow['description']);

        return $role;
    }

    /**
     * Get All Contacts for the given node
     *
     * @param tx_caretaker_AbstractNode $node
     * @return array
     */
    public function getContactsByNode(tx_caretaker_AbstractNode $node)
    {
        $contacts = array();

        // only Instancegroups and Instances store Contacts
        $nodeType = $node->getType();
        if ($nodeType != tx_caretaker_Constants::nodeType_Instance && $nodeType != tx_caretaker_Constants::nodeType_Instancegroup) {
            return $contacts;
        }

        $storageTable = $node->getStorageTable();
        $table = tx_caretaker_Constants::relationTable_Node2Address;
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $statement = $queryBuilder
            ->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('uid_node',
                    $queryBuilder->createNamedParameter($node->getUid(), PDO::PARAM_INT))
            )
            ->andWhere(
                $queryBuilder->expr()->eq('node_table',
                    $queryBuilder->createNamedParameter($storageTable, PDO::PARAM_STR))
            )
            ->execute();
        while ($row = $statement->fetch()) {
            if ($contact = $this->dbrow2contact($row)) {
                $contacts[] = $contact;
            }
        }

        return $contacts;
    }

    /**
     * Get All Contacts for the given node that match the given role
     *
     * @param tx_caretaker_AbstractNode $node
     * @param tx_caretaker_ContactRole $role
     * @return array
     */
    public function getContactsByNodeAndRole(tx_caretaker_AbstractNode $node, tx_caretaker_ContactRole $role)
    {
        $contacts = array();

        // only Instancegroups and Instances store Contacts
        $nodeType = $node->getType();
        if ($nodeType != tx_caretaker_Constants::nodeType_Instance && $nodeType != tx_caretaker_Constants::nodeType_Instancegroup) {
            return $contacts;
        }

        $storageTable = $node->getStorageTable();
        $table = tx_caretaker_Constants::relationTable_Node2Address;
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $statement = $queryBuilder
            ->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('uid_node',
                    $queryBuilder->createNamedParameter($node->getUid(), PDO::PARAM_INT))
            )
            ->andWhere(
                $queryBuilder->expr()->eq('node_table',
                    $queryBuilder->createNamedParameter($storageTable, PDO::PARAM_STR))
            )
            ->andWhere(
                $queryBuilder->expr()->eq('role',
                    $queryBuilder->createNamedParameter($role->getUid(), PDO::PARAM_INT))
            )
            ->execute();
        while ($row = $statement->fetch()) {
            if ($contact = $this->dbrow2contact($row)) {
                $contacts[] = $contact;
            }
        }

        return $contacts;
    }

    /**
     * Convert node address relation record to contact object
     *
     * @parem array $row
     * @param mixed $row
     */
    private function dbrow2contact($row)
    {
        $address = false;
        if ($row['uid_address']) {
            $table = tx_caretaker_Constants::table_ContactAddresses;
            if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('tt_address')) {
                $table = tx_caretaker_Constants::table_TTAddressAddresses;
            }
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
            $statement = $queryBuilder
                ->select('*')
                ->from($table)
                ->where(
                    $queryBuilder->expr()->eq('uid',
                        $queryBuilder->createNamedParameter($row['uid_address'], PDO::PARAM_INT))
                )
                ->andWhere($queryBuilder->expr()->eq('hidden', 0))
                ->andWhere($queryBuilder->expr()->eq('deleted', 0))
                ->setMaxResults(1)
                ->execute();
            $address_row = $statement->fetch();
            if ($address_row) {
                $address = $address_row;
            } else {
                return false;
            }
        } else {
            return false;
        }

        if ($row['role']) {
            $role = $this->getContactRoleByUid($row['role']);
        } else {
            $role = false;
        }

        return new tx_caretaker_Contact($address, $role);
    }
}
