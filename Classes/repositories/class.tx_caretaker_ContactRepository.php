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
 * Repository to handle the storing and reconstruction of node contacts
 * aggregatorResults. The whole object <-> database
 * communication happens here.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
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
            self::$instance = new tx_caretaker_ContactRepository();
        }

        return self::$instance;
    }

    /**
     * Get Role Object for given Uid
     *
     * @param <type> $uid
     * @return  tx_caretaker_ContactRole
     */
    public function getContactRoleByUid($uid)
    {
        $rolesRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', tx_caretaker_Constants::table_Roles, 'uid = ' . intval($uid) . ' AND hidden=0 AND deleted=0');
        if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($rolesRes)) {
            return $this->dbrow2contact_role($row);
        } else {
            return false;
        }
    }

    /**
     * Get Role Object for given String
     *
     * @param string $id
     * @return tx_caretaker_ContactRole
     */
    public function getContactRoleById($id)
    {
        $rolesRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', tx_caretaker_Constants::table_Roles, 'id = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id, tx_caretaker_Constants::table_Roles) . ' AND hidden=0 AND deleted=0');
        if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($rolesRes)) {
            return $this->dbrow2contact_role($row);
        } else {
            return false;
        }
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
        $contacts = [];

        // only Instancegroups and Instances store Contacts
        $nodeType = $node->getType();
        if ($nodeType != tx_caretaker_Constants::nodeType_Instance && $nodeType != tx_caretaker_Constants::nodeType_Instancegroup) {
            return $contacts;
        }

        $storageTable = $node->getStorageTable();
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', tx_caretaker_Constants::relationTable_Node2Address, 'uid_node=' . $node->getUid() . ' AND node_table=\'' . $storageTable . '\'');
        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
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

        $contacts = [];

        // only Instancegroups and Instances store Contacts
        $nodeType = $node->getType();
        if ($nodeType != tx_caretaker_Constants::nodeType_Instance && $nodeType != tx_caretaker_Constants::nodeType_Instancegroup) {
            return $contacts;
        }

        $storageTable = $node->getStorageTable();
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', tx_caretaker_Constants::relationTable_Node2Address, 'uid_node=' . $node->getUid() . ' AND node_table=\'' . $storageTable . '\'' . ' AND role=' . $role->getUid());
        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
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
     */
    private function dbrow2contact($row)
    {
        $address = false;
        if ($row['uid_address']) {
            $table = tx_caretaker_Constants::table_ContactAddresses;
            if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('tt_address')) {
                $table = tx_caretaker_Constants::table_TTAddressAddresses;
            }
            $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $table, 'uid=' . $row['uid_address'] . ' AND hidden=0 AND deleted=0', '', '', 1);
            $address_row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
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

