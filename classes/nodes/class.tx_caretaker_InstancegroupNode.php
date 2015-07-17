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
 * Caretaker-node which represents an group of monitored
 * caretaker-instanceNodes. The groups ca be recursive.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_InstancegroupNode extends tx_caretaker_AggregatorNode {
	/**
	 * Constructor
	 *
	 * @param integer $uid
	 * @param string $title
	 * @param tx_caretaker_AbstractNode $parent
	 * @param boolean $hidden
	 */
	public function __construct($uid, $title, $parent, $hidden = 0) {
		parent::__construct($uid, $title, $parent, tx_caretaker_Constants::table_Instancegroups, tx_caretaker_Constants::nodeType_Instancegroup, $hidden);
	}

	/**
	 * Get the caretaker node id of this node
	 * return string
	 */
	public function getCaretakerNodeId() {
		return 'instancegroup_' . $this->getUid();
	}

	/**
	 * Find Child nodes of this Instancegroup
	 * @param boolean $show_hidden
	 * @return array
	 * @see caretaker/trunk/classes/nodes/tx_caretaker_AggregatorNode#findChildren()
	 */
	protected function findChildren($show_hidden = FALSE) {
		$node_repository = tx_caretaker_NodeRepository::getInstance();
		$subgroups = $node_repository->getInstancegroupsByParentGroupUid($this->uid, $this, $show_hidden);
		$instances = $node_repository->getInstancesByInstancegroupUid($this->uid, $this, $show_hidden);
		return array_merge($subgroups, $instances);
	}

	/**
	 * Find Parent Node
	 * @return tx_caretaker_AbstractNode
	 */
	protected function findParent() {
		$node_repository = tx_caretaker_NodeRepository::getInstance();
		$parent = $node_repository->getInstancegroupByChildGroupUid($this->uid, $this);
		return $parent;
	}
}

?>