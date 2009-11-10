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
		return $node_repository->getNode($instancegroupId, $instanceId, $testgroupId, $testId, $show_hidden);
	}
	
	/**
	 * Get the Identifier String for a Node
	 * 
	 * @param tx_caretaker_AbstractNode $node
	 * @return string
	 */
	static function node2id ($node){
		$node_repository    = tx_caretaker_NodeRepository::getInstance();
		return $node_repository->node2id ($node);
	}

	/**
	 * Get the Node Object for a given Identifier String
	 * 
	 * @param string $id_string
	 * @param boolean $show_hidden
	 * @return tx_caretaker_AbstractNode
	 */
	static function id2node ($id_string, $show_hidden=false){
		$node_repository    = tx_caretaker_NodeRepository::getInstance();
		return $node_repository->id2node ($id_string, $show_hidden);
	}
	
	static function getRootNode (){
		$node_repository    = tx_caretaker_NodeRepository::getInstance();
		return $node_repository->getRootNode();
	}

	/**
	 * Translate a given string in the current language
	 *
	 * @param string $string
	 * @return string
	 */
	static function locallizeString( $locallang_string ){

		$locallang_parts = explode (':',$locallang_string);

		if( array_shift($locallang_parts) != 'LLL') {
			return $locallang_string;
		}

		switch (TYPO3_MODE){
			case 'FE':

				$lcObj  = t3lib_div::makeInstance('tslib_cObj');
				return( $lcObj->TEXT(array('data' => $locallang_string )) );

			case 'BE':

				$locallang_key   = array_pop($locallang_parts);
				$locallang_file  = implode(':',$locallang_parts);

				$language_key  = $GLOBALS['BE_USER']->uc['lang'];
				$LANG = t3lib_div::makeInstance('language');
				$LANG->init($language_key);

				return $LANG->getLLL($locallang_key, $LANG->readLLfile(t3lib_div::getFileAbsFileName( $locallang_file )));

			default :

				return $locallang_string;


		}

	}
	
}
?>