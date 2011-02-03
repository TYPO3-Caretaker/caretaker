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
 * Caretaker-node that represents a group of caretaker-tests which are assigned
 * to instances. Testgroups can be defined recursively.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_TestgroupNode extends tx_caretaker_AggregatorNode {
	
	/**
	 * Constructor 
	 * 
	 * @param integer $uid
	 * @param string $title
	 * @param tx_caretaker_AbstractNode $parent
	 * @param boolean $hidden
	 */
	public function __construct($uid, $title, $parent, $hidden=0){
		parent::__construct($uid, $title, $parent, tx_caretaker_Constants::table_Testgroups, tx_caretaker_Constants::nodeType_Testgroup, $hidden );
	}

	/**
	 * Get the caretaker node id of this node
	 * return string
	 */
	public function getCaretakerNodeId(){
		$instance = $this->getInstance();
		return 'instance_'.$instance->getUid().'_testgroup_'.$this->getUid();
	}

	/**
	 * (non-PHPdoc)
	 * @see caretaker/trunk/classes/nodes/tx_caretaker_AggregatorNode#findChildren()
	 */
	protected function findChildren ($show_hidden=false){
		
			// read subgroups
		$node_repository = tx_caretaker_NodeRepository::getInstance();
		$subgroups = $node_repository->getTestgroupsByParentGroupUid($this->uid, $this , $show_hidden );
			// read instances
		$tests = $node_repository->getTestsByGroupUid($this->uid, $this, $show_hidden);
			// save
		$children = array_merge($subgroups, $tests);
		return $children;
	}



}

?>