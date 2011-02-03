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
 * @package TYPO3
 * @subpackage caretaker
 */
abstract class tx_caretaker_AbstractNode {

	/**
	 * UID
	 * @var integer
	 */
	protected $uid       = false;
	
	/**
	 * Title
	 * @var string
	 */
	protected $title     = false;
	
	/**
	 * Type
	 * @var string
	 */
	protected $type      = '';

	/**
	 * Description
	 * @var string
	 */
	protected $description = '';
	
	/**
	 * Hidden
	 * @var boolean
	 */
	protected $hidden    = false;
	
	
	/**
	 * Parent Node
	 * @var tx_caretaker_AbstractNode
	 */
	protected $parent    = NULL;
	
	/**
	 * 
	 * @var unknown_type
	 */
	protected $notification_address_ids = array();


	/**
	 * Associatiove array ob DB-Row
	 *
	 * @var array
	 */
	protected $dbRow = NULL;

	/**
	 * The table where this node is stored
	 *
	 * @var string
	 */
	protected $storageTable = '';
	
	/**
	 * Constructor
	 * 
	 * @param integer $uid
	 * @param string $title
	 * @param tx_caretaker_AbstractNode $parent
	 * @param string $type
	 * @param string $hidden
	 */
	public function __construct( $uid, $title, $parent, $storageTable, $type='', $hidden = false ){
		$this->uid    = $uid;
		$this->title  = $title;
		$this->parent = $parent;
		$this->type   = $type;
		$this->storageTable = $storageTable;
		if ($parent && $parent->getHidden()){
			$this->hidden = true;
		} else {
			$this->hidden = (boolean)$hidden;
		}
	}

	/**
	 * Set the description
	 * @param string $decription
	 */
	public function setDescription($decription){
		$this->description = $decription;
	}
	
	/**
	 * Get the caretaker node id of this node
	 * return string
	 */
	abstract public function getCaretakerNodeId();

	/**
	 * Get the uid
	 * @return integer 
	 */
	public function getUid(){
		return $this->uid;
	}

	/**
	 * Get the parent node
	 * @return tx_caretaker_AbstractNode
	 */
	public function getParent(){
		return $this->parent;
	}

	/**
	 * Returns the tablename where this type of node is stored
	 * 
	 * @return string
	 */
	public function getStorageTable() {
		return $this->storageTable;
	}

	/**
	 * set hidden state
	 * @param boolean boolean
	 */
	public function setHidden($hidden = true){
		$this->hidden = (boolean)$hidden;
	}
	
	/**
	 * Get hidden state
	 * @return boolean
	 */
	public function getHidden(){
		return $this->hidden;
	}
		
	/**
	 * Get the Title
	 * @return string
	 */
	public function getTitle(){
		return $this->title;
	}
	
	/**
	 * Get the Description
	 * @return string
	 * @deprecated
	 */
	public function getDescription(){
		return $this->description;
	}
	
	/**
	 * Get the node type
	 * @return string
	 */
	public function getType(){
		return $this->type;
	}	

	/**
	 * Get the all tests wich can be found below this node
	 * @return array
	 */
	abstract public function getTestNodes();
	
	/**
	 * Save the dbRow Array to the node
	 *
	 * @param array $dbRow
	 */
	public function setDbRow($dbRow){
		$this->dbRow = $dbRow;
	}
	
	/**
	 * Get a property from node-dbRow
	 * 
	 * @param string $fieldname
	 */
	public function getProperty($fieldname){
		if (!$this->dbRow || !is_array($this->dbRow)) {
			return false;
		}

		if (isset($this->dbRow[$fieldname])){
			return $this->dbRow[$fieldname];
		}  else {
			return false;
		}

	}

	/**
	 * Get the description of the Testsevice
	 * @return string
	 * @deprecated
	 */
	public function getTypeDescription(){
		return '';
	}

	/**
	 * Get the configuration infotext
	 *
	 * @return string
	 * @deprecated
	 */
	public function getConfigurationInfo(){
		return '';
	}

	/**
	 * Get the info weather a node is hidden
	 *
	 * @return string
	 * @deprecated
	 */
	public function getHiddenInfo(){
		return ($this->getHidden() ? 'yes' : 'no');
	}
	
	/** 
	 * Get a Description for the Node Value
	 *
	 * @return string
	 * @deprecated
	 */
	abstract public function getValueDescription();

	/**
	 * Get the current Instance if 
	 * @return tx_caretaker_InstanceNode
	 */
	public function getInstance(){
		
		if ( is_a($this, 'tx_caretaker_InstanceNode') ){
			return $this;
		} else if ($this->parent){
			return $this->parent->getInstance();
		} else {
			return false;
		}
	}
	
	/**
	 * Update the NodeState (Execute Test)
	 *
 	 * @param boolean $force_update
	 * @return tx_caretaker_NodeResult
	 */
	abstract public function updateTestResult($force_update = false);
	
	/**
	 * Read current Node Result
	 * 
	 * @return tx_caretaker_NodeResult
	 */	
	abstract public function getTestResult();
	
	/**
	 * Get ResultRange for specified Time
	 *  
	 * @param integer $startdate
	 * @param integer $stopdate
	 * @return tx_caretaker_NodeResultRange
	 */
	abstract public function getTestResultRange($startdate, $stopdate);

	/**
	 * Get the Number of available Testresults
	 * @return interger
	 */
	abstract public function getTestResultNumber();


	/**
	 * Get Test Result Objects 
	 *
	 * @param integer $offset
	 * @param integer $limit
	 */
	abstract public function getTestResultRangeByOffset($offset=0, $limit=10);

	/**
	 * Send a notification to all registered notofication services
	 *
	 * @param tx_caretaker_TestResult $result
	 * @param tx_caretaker_TestResult $lastResult
	 */
	public function notify ($event, $result = NULL, $lastResult = NULL ){
			// find all registered notification services
		$notificationServices = tx_caretaker_ServiceHelper::getAllCaretakerNotificationServices();
		foreach ( $notificationServices as $notificationService ){
			$notificationService->addNotification( $event, $this, $result, $lastResult );
		}
	}

	/**
	 * Get the contacts for the node
	 * 
	 * @param tx_caretaker_ContactRole $role
	 * @param boolean $firstResultsOnly
	 * @param boolean $recursiveSearch
	 */
	public function getContacts( $role = NULL , $firstResultsOnly = false, $recursiveSearch = true ){

		$contactRepository = tx_caretaker_ContactRepository::getInstance();
		$contacts = array();

		$node = $this;
		while ( $node ){

			if ($role != NULL){
				$node_contacts = $contactRepository->getContactsByNodeAndRole( $node, $role );
			} else {
				$node_contacts = $contactRepository->getContactsByNode( $node );
			}

			$contacts = array_merge($contacts, $node_contacts);

				// return only first found results if selected
			if ( count($contacts)>0 && $firstResultsOnly == true ){
				return $contacts;
			}

				// go up the nodetree if recursiveSearch is set
			if ($recursiveSearch == true){
				$node = $node->getParent();
			} else {
				break;
			}

		}

		return $contacts;
		
	}

	

	/**
	 * Returns all contacts that are set for this node. With default settings it will return the contacts
	 * of the first node found in the rootline. The alternative behavior is to return all from all nodes
	 * in the rootline.
	 *
	 * Please note that only instances and instance_groups can have contacts! You can call it for every
	 * node though.
	 *
	 * @param array		$roles: A set of requested roles. The field "id" is used for selection!
	 * @param boolean	$returnFirst: If set to true, the first found set of contacts will be returned
	 *
	 * @return array with contacts
	 *
	 * @deprecated use getContacts instead
	 * @see getContacts
	 * @todo remove this method
	 */
	public function findContacts(array $roles = array(), $returnFirst = true) {
		$contacts = array();
		
			// fetch ids of requested roles
		$roleSelect = '';

		if (count($roles) > 0) {
			$rolesRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,id', tx_caretaker_Constants::table_Roles, 'id IN (\''.implode('\',\'', $roles).'\') AND hidden=0 AND deleted=0');
		} else {
			$rolesRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,id', tx_caretaker_Constants::table_Roles, 'hidden=0 AND deleted=0');
		}

		$roles = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($rolesRes)) {
			$roles[$row['uid']] = $row['id'];
		}
		
		if (count($roles) > 0) {
			$roleSelect = 'role IN ('.implode(',', array_keys($roles)).') AND ';
		}

		$processedNodeList = array();

		$node = $this;
		while ($node) {
			$nodeType = $node->getType();
			$nodeUid = $node->getUid();

			if ($nodeType == tx_caretaker_Constants::nodeType_Instance || $nodeType == tx_caretaker_Constants::nodeType_Instancegroup) {
					// which table?
				$storageTable = $this->getStorageTable();
								
					// select relations and store them internally
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', tx_caretaker_Constants::relationTable_Node2Address, 'uid_node='.$nodeUid.' AND '.$roleSelect.'node_table=\''.$storageTable.'\'');
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
						// don't add relations if we only interested in the first one in rootline
					if ($returnFirst && isset($relations[$row['role']]) && in_array($nodeUid, $processedNodeList)) continue;
					
						// fetch address and add to return structure
					$address = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', tx_caretaker_Constants::table_Addresses, 'uid='.$row['uid_address'], '', '', 1);
					$contacts[$roles[$row['role']]][$row['uid_address']] = $address[0];
				}
			}

			$processedNodeList[] = $nodeUid;
			$node = $node->getParent();
		}

		return $contacts;
	}
}
?>