<?php
/**
 * This is a file of the caretaker project.
 * Copyright 2008 by n@work Internet Informationssystem GmbH (www.work.de)
 * 
 * @Author	Thomas Hempel 		<thomas@work.de>
 * @Author	Martin Ficzel		<martin@work.de>
 * @Author	Patrick Kollodzik	<patrick@work.de> 
 * @Author	Tobias Liebig   	<mail_typo3.org@etobi.de>
 * @Author	Christopher Hlubek	<hlubek@networkteam.com>
 * 
 * $Id$
 */

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008 Martin Ficzel <ficzel@work.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once (t3lib_extMgm::extPath('caretaker').'/interfaces/interface.tx_caretaker_LoggerInterface.php');
require_once (t3lib_extMgm::extPath('caretaker').'/interfaces/interface.tx_caretaker_NotifierInterface.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/results/class.tx_caretaker_NodeResultRange.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_Helper.php');

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
	 * Logger
	 * @var tx_caretaker_LoggerInterface
	 */
	protected $logger    = false;
	
	/**
	 * Notifier
	 * @var tx_caretaker_NotificationInterface
	 */
	protected $notifier  = false;
	
	/**
	 * 
	 * @var unknown_type
	 */
	protected $notification_address_ids = array();
	
	/**
	 * Constructor
	 * 
	 * @param integer $uid
	 * @param string $title
	 * @param tx_caretaker_AbstractNode $parent
	 * @param string $type
	 * @param string $hidden
	 */
	public function __construct( $uid, $title, $parent, $type='', $hidden = false ){
		$this->uid    = $uid;
		$this->title  = $title;
		$this->parent = $parent;
		$this->type   = $type;
		$this->hidden = (boolean)$hidden;
	}

	/**
	 * Set the description
	 * @param string $decription
	 */
	public function setDescription($decription){
		$this->description = $decription;
	}
	
	/**
	 * Get the uid
	 * @return integer 
	 */
	public function getUid(){
		return $this->uid;
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
	 * Get a Description for the Node Value
	 * @return string
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
	
	/*
	 * Update Node Result and store in DB. 
	 * 
	 * @param boolean Force update of children
	 * @return tx_caretaker_NodeResult
	 */
	
	/**
	 * Update the NodeState (Execute Test)
 	 * @param boolean $force_update
	 * @return tx_caretaker_NodeResult
	 */
	abstract public function updateTestResult($force_update = false);
	
	/**
	 * Read current Node Result
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
	 * @return interger Number of available Testresults
	 */
	abstract public function getTestResultNumber();


	/**
	 * Get Test Result Objects
	 *
	 * @param integer $offset
	 * @param integer $limit
	 */
	abstract public function getTestResultRangeByOffset($offset=0, $limit=10);

	
	/*
	 * Logging Methods
	 */
	
	/**
	 * Set the current Logger
	 * 
	 * @param tx_caretaker_LoggerInterface $logger
	 */
	public function setLogger (tx_caretaker_LoggerInterface $logger){
		$this->logger = $logger;
	}
	
	/**
	 * Add Message to Log
	 * 
	 * @param string $message
	 * @param boolean $add_node_info
	 */
	public function log($message, $add_node_info=true){
		if ($add_node_info){
			$indented_message = array();
			foreach ( explode(chr(10), $message) as $number => $line ){
				if ($number == 0){
					$indented_message[] = ' +- '.$this->type.' '.$this->title.'['.$this->uid.'] '.$line;
				} else {
					$indented_message[] = '    '.$line;
				}
			}
		} else {
			$indented_message = Array($message);
		}

		foreach ($indented_message as $message){
			if ($this->logger){
				$this->logger->log($message);
			} else if ($this->parent) {
				$this->parent->log(' | '.$message , false);
			}
		}
	}
	
	/*
	 * ###########################
	 * ### Notification Methods ##
	 * ###########################
	 */

	/**
	 * Add a list of tt_address UIDs for Notification
	 *
	 * @param $id_array
	 * @return unknown_type
	 */
	public function setNotificationIds($notification_address_ids){
		$this->notification_address_ids = $notification_address_ids;
	}

	/**
	 * Get the list of notifications
	 *
	 * @return array Array of tt_address uids
	 */
	public function getNotificationIds ($include_parent_notification_ids = false){
		$notification_address_ids = $this->notification_address_ids;
		if ($include_parent_notification_ids && $this->parent) {
			$notification_address_ids = array_merge($notification_address_ids,  $this->parent->getNotificationIds($include_parent_notification_ids) );
			$notification_address_ids = array_unique($notification_address_ids);
		}
		return $notification_address_ids;
	}

	/**
	 * Set the current Notifier
	 *
	 * @param tx_caretaker_NotifierInterface $notifier
	 */
	public function setNotifier (tx_caretaker_NotifierInterface $notifier){
		$this->notifier = $notifier;
	}
	
	/**
	 * Pass Notification to Notifier or Parent
	 *
	 * @param array $recipients
	 * @param integer $state
	 * @param string $msg
	 * @param string $description
	 * @param integer $node_id
	 */
	private function notify( $recipients, $state, $msg = '' , $description = '' ,$node_id ){
		if ($this->notifier){
			$this->notifier->addNotification($recipients, $state, $msg, $description, $node_id);
		} else if ($this->parent) {
			$this->parent->notify($recipients, $state, $msg, $description, $node_id);
		}
	}

	/**
	 * Add a Nofitcation
	 *
	 * @param integer $state
	 * @param string $msg
	 */
	public function sendNotification( $state, $msg){
		$notification_address_ids = $this->getNotificationIds(true);
		if ( count($notification_address_ids) > 0 ){
			foreach($notification_address_ids as $notfificationId){
				$this->notify( $notfificationId, $state, $this->type.' '.$this->title.'['.$this->uid.'] '.$msg, $this->description, tx_caretaker_Helper::node2id($this) );
			}
		}
	}
	
}
?>