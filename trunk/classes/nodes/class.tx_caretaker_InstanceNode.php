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

class tx_caretaker_InstanceNode extends tx_caretaker_AggregatorNode {

	/**
	 * URL to acces this instance
	 * @var string
	 */
	protected $url;
	
	/**
	 * Hostname
	 * @var string
	 */
	protected $hostname;

	/**
	 * Public key
	 * @var string
	 */
	protected $publicKey;

	/**
	 * test configuration overlay to overwritte tests default configurations
	 * @var array
	 */
	protected $testConfigurationOverlay;
	
	/**
	 * Constructor 
	 * 
	 * @param integer $uid
	 * @param string $title
	 * @param tx_caretaker_AbstractNode $parent
	 * @param string $url
	 * @param string $host
	 * @param string $ip
	 * @param boolean $hidden
	 */
	public function __construct( $uid, $title, $parent, $url='', $hostname='', $publicKey = '', $hidden=0) {
		parent::__construct($uid, $title, $parent, 'Instance', $hidden);
		$this->url = $url;
		$this->hostname = $hostname;
		$this->publicKey = $publicKey;
	}

	/**
	 * Get the url
	 * @return string
	 */
	public function getUrl (){
		return $this->url;
	}
	
	/**
	 * Get the hostname
	 * @return unknown_type
	 */
	public function getHostname (){
		return $this->hostname;
	}
	
	/**
	 * Get the public key
	 * @return string
	 */
	public function getPublicKey(){
		return $this->publicKey;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see caretaker/trunk/classes/nodes/tx_caretaker_AggregatorNode#findChildren()
	 */
	public function findChildren ($show_hidden=false){
		
		$node_repository = tx_caretaker_NodeRepository::getInstance();
		
		$testgroups = $node_repository->getTestgroupsByInstanceUid($this->uid, $this, $show_hidden);
		$tests =      $node_repository->getTestsByInstanceUid($this->uid, $this, $show_hidden);
		
		$children = array_merge($testgroups, $tests);
		return $children;
	}
	
	public function setTestConfigurations($data) {
		$this->testConfigurationOverlay= t3lib_div::xml2array($data);
	}

}

?>
