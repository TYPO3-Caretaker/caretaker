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
 * Baseclass for all caretaker nodes which form the caretaker nodeTree.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 */
abstract class tx_caretaker_AbstractNode
{
    /**
     * UID
     *
     * @var int
     */
    protected $uid = false;

    /**
     * Title
     *
     * @var string
     */
    protected $title = false;

    /**
     * Type
     *
     * @var string
     */
    protected $type = '';

    /**
     * Description
     *
     * @var string
     */
    protected $description = '';

    /**
     * Hidden
     *
     * @var bool
     */
    protected $hidden = false;

    /**
     * Parent Node
     *
     * @var tx_caretaker_AbstractNode
     */
    protected $parent;

    /**
     * @var array
     */
    protected $notification_address_ids = array();

    /**
     * Associative array of DB-Row
     *
     * @var array
     */
    protected $dbRow;

    /**
     * The table where this node is stored
     *
     * @var string
     */
    protected $storageTable = '';

    /**
     * @var array Array of contacts group by role
     */
    protected $contacts = array();

    /**
     * Constructor
     *
     * @param int $uid
     * @param string $title
     * @param tx_caretaker_AbstractNode $parent
     * @param string $storageTable
     * @param string $type
     * @param string|bool $hidden
     */
    public function __construct($uid, $title, $parent, $storageTable, $type = '', $hidden = false)
    {
        $this->uid = $uid;
        $this->title = $title;
        $this->parent = $parent;
        $this->type = $type;
        $this->storageTable = $storageTable;
        if ($parent && $parent->getHidden()) {
            $this->hidden = true;
        } else {
            $this->hidden = (bool)$hidden;
        }
    }

    /**
     * Set the description
     *
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get the caretaker node id of this node
     *
     * @return string
     */
    abstract public function getCaretakerNodeId();

    /**
     * Get the uid
     *
     * @return int
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Get the parent node
     *
     * @return tx_caretaker_AbstractNode
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Returns the table name where this type of node is stored
     *
     * @return string
     */
    public function getStorageTable()
    {
        return $this->storageTable;
    }

    /**
     * Set hidden state
     *
     * @param bool
     * @param mixed $hidden
     */
    public function setHidden($hidden = true)
    {
        $this->hidden = (bool)$hidden;
    }

    /**
     * Get hidden state
     *
     * @return bool
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Get the Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the Description
     *
     * @return string
     * @deprecated
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get the node type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the all tests wich can be found below this node
     *
     * @return array
     */
    abstract public function getTestNodes();

    /**
     * Save the dbRow Array to the node
     *
     * @param array $dbRow
     */
    public function setDbRow($dbRow)
    {
        $this->dbRow = $dbRow;
    }

    /**
     * Get a property from node-dbRow
     *
     * @param string $fieldname
     * @return mixed
     */
    public function getProperty($fieldname)
    {
        if (!$this->dbRow || !is_array($this->dbRow)) {
            return false;
        }

        if (isset($this->dbRow[$fieldname])) {
            return $this->dbRow[$fieldname];
        }
        return false;
    }

    /**
     * Get the description of the Testservice
     *
     * @return string
     */
    public function getTypeDescription()
    {
        return '';
    }

    /**
     * Get the configuration info text
     *
     * @return string
     */
    public function getConfigurationInfo()
    {
        return '';
    }

    /**
     * Get the info weather a node is hidden
     *
     * @return string
     */
    public function getHiddenInfo()
    {
        return $this->getHidden() ? 'yes' : 'no';
    }

    /**
     * Get a Description for the node value
     *
     * @return string
     * @deprecated
     */
    abstract public function getValueDescription();

    /**
     * Get the current instance
     *
     * @return tx_caretaker_InstanceNode
     */
    public function getInstance()
    {
        if ($this instanceof tx_caretaker_InstanceNode) {
            return $this;
        } elseif ($this->parent) {
            return $this->parent->getInstance();
        }
        return false;
    }

    /**
     * Update the Node State (Execute Test)
     *
     * @param array $options
     * @return tx_caretaker_NodeResult
     */
    abstract public function updateTestResult($options = array());

    /**
     * Read current Node Result
     *
     * @return tx_caretaker_NodeResult
     */
    abstract public function getTestResult();

    /**
     * Get ResultRange for specified time
     *
     * @param int $startdate
     * @param int $stopdate
     * @return tx_caretaker_NodeResultRange
     */
    abstract public function getTestResultRange($startdate, $stopdate);

    /**
     * Get the Number of available test results
     *
     * @return int
     */
    abstract public function getTestResultNumber();

    /**
     * Get Test Result Objects
     *
     * @param int $offset
     * @param int $limit
     * @return tx_caretaker_NodeResultRange
     */
    abstract public function getTestResultRangeByOffset($offset = 0, $limit = 10);

    /**
     * @param tx_caretaker_TestResult $result
     * @return tx_caretaker_TestResult
     */
    public function getPreviousDifferingResult($result)
    {
        $testResultRepository = tx_caretaker_TestResultRepository::getInstance();

        return $testResultRepository->getPreviousDifferingResult($this, $result);
    }

    /**
     * Send a notification to all registered notification services
     *
     * @param string $event
     * @param tx_caretaker_NodeResult $result
     * @param tx_caretaker_NodeResult $lastResult
     */
    public function notify($event, $result = null, $lastResult = null)
    {
        // find all registered notification services
        $notificationServices = tx_caretaker_ServiceHelper::getAllCaretakerNotificationServices();
        /** @var tx_caretaker_AbstractNotificationService $notificationService */
        foreach ($notificationServices as $notificationService) {
            $notificationService->addNotification($event, $this, $result, $lastResult);
        }
    }

    /**
     * Get the contacts for the node
     *
     * @param string|tx_caretaker_ContactRole|array<tx_caretaker_ContactRole> $roles
     * @return array
     */
    public function getContacts($roles = null)
    {
        $contactRepository = tx_caretaker_ContactRepository::getInstance();

        if ($roles instanceof tx_caretaker_ContactRole) {
            $roles = array($roles);
        } elseif (is_string($roles)) {
            $roleIds = $roles;
            $roles = array();
            foreach (\TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $roleIds) as $roleId) {
                if ($roleId === '*') {
                    $roles = null;
                    break;
                }
                $role = $contactRepository->getContactRoleById($roleId);
                if (!$role instanceof tx_caretaker_ContactRole) {
                    $role = $contactRepository->getContactRoleByUid(intval($roleId));
                }
                if ($role instanceof tx_caretaker_ContactRole) {
                    $roles[] = $role;
                }
            }
        }

        $contacts = array();
        if ($roles === null) {
            if ($this->contacts['__all__'] === null) {
                $this->contacts['__all__'] = $contactRepository->getContactsByNode($this);
            }
            $contacts = $this->contacts['__all__'];
        } else {
            /** @var tx_caretaker_ContactRole $role */
            foreach ($roles as $role) {
                if ($this->contacts[$role->getId()] === null) {
                    $this->contacts[$role->getId()] = $contactRepository->getContactsByNodeAndRole($this, $role);
                }
                $contacts = array_merge($this->contacts[$role->getId()], $contacts);
            }
        }

        if ($this->getParent()) {
            $contacts = array_merge($this->getParent()->getContacts($roles), $contacts);
        }

        // TODO return a list with only unique entries
        return $contacts;
    }

    /**
     * @return array|bool
     */
    public function getStrategies()
    {
        return $this->getParent() ? $this->getParent()->getStrategies() : false;
    }

    /**
     * @param int $testUid
     * @return bool
     */
    public function getTestConfigurationOverlayForTestUid($testUid)
    {
        return false;
    }
}
