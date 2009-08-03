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

require_once (t3lib_extMgm::extPath('caretaker').'/classes/repositories/class.tx_caretaker_NodeRepository.php');

class tx_caretaker_Helper {
	
	/**
	 * Retrieve a specific Node 
	 * 
	 * @param integer $instancegroupId
	 * @param integer $instanceId
	 * @param integer $testgroupId
	 * @param integer $testId
	 * @param boolean $show_hidden
	 * @return tx_caretaker_AbstractNode
	 */
	static function getNode($instancegroupId = false, $instanceId = false, $testgroupId = false, $testId = false, $show_hidden=false){
		
		$node_repository    = tx_caretaker_NodeRepository::getInstance();

		$instancegroupId = (int)$instancegroupId;
		$instanceId      = (int)$instanceId;
		$testgroupId     = (int)$testgroupId;
		$testId          = (int)$testId;
		
		if ($instancegroupId>0){
			$instancegroup = $node_repository->getInstancegroupByUid($instancegroupId, false, $show_hidden);
			if ($instancegroup) return $instancegroup;
		} else if ($instanceId>0){
			$instance = $node_repository->getInstanceByUid($instanceId, false, $show_hidden);
			if ($instance) {
				if ($testgroupId>0){
					$group = $node_repository->getTestgroupByUid($testgroupId, $instance, $show_hidden);
					if ($group) return $group;		
	    		} else if ($testId>0) {
					$test = $node_repository->getTestByUid($testId, $instance, $show_hidden);
					if ($test) return $test;		
	    		} else {
					return $instance;		
				}
			}
		} 
		return false;
	}
	
	/**
	 * Get the Identifier String for a Node
	 * 
	 * @param tx_caretaker_AbstractNode $node
	 * @return string
	 */
	static function node2id ($node){
		$id = false;	
		switch (get_class ($node)){
			case 'tx_caretaker_InstancegroupNode':
				$id = 'instancegroup_'.$node->getUid();
				break;
			case 'tx_caretaker_InstanceNode':
				$id = 'instance_'.$node->getUid();
				break;
			case 'tx_caretaker_TestgroupNode':
				$instance = $node->getInstance();
				$id = 'instance_'.$instance->getUid().'_testgroup_'.$node->getUid();
				break;
			case 'tx_caretaker_TestNode':
				$instance = $node->getInstance();
				$id = 'instance_'.$instance->getUid().'_test_'.$node->getUid();
				break;	
			case 'tx_caretaker_RootNode':
				$instance = $node->getInstance();
				$id = 'root';
				break;	
			
		}
		return $id;
	}
	
	/**
	 * Get the Node Object for a given Identifier String
	 * 
	 * @param string $id_string
	 * @param boolean $show_hidden
	 * @return tx_caretaker_AbstractNode
	 */
	static function id2node ($id_string, $show_hidden=false){
				
		if ($id_string == 'root') return tx_caretaker_Helper::getRootNode();
		
		$parts = explode('_', $id_string);
		$info  = array();
		for($i=0; $i<count($parts);$i +=2 ){
			switch ($parts[$i]){
				case 'instancegroup':
					$info['instancegroup']=(int)$parts[$i+1];
					break;
				case 'instance':
					$info['instance']=(int)$parts[$i+1];
					break;
				case 'testgroup':
					$info['testgroup']=(int)$parts[$i+1];
					break;
				case 'test':
					$info['test']=(int)$parts[$i+1];
					break;
			}
		}
		return tx_caretaker_Helper::getNode($info['instancegroup'],$info['instance'],$info['testgroup'],$info['test'], $show_hidden );
	}
	
	static function getRootNode (){
		$node_repository    = tx_caretaker_NodeRepository::getInstance();
		return $node_repository->getRootNode();
	}
	
}
?>