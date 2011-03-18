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
 * The root of all caretaker-nodes. It has no representation
 * in the database nor user-settings.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_RootNode extends tx_caretaker_AggregatorNode {

	/**
	 * @param bool $hidden
	 */
	public function __construct($hidden = FALSE) {
		parent::__construct(0, 'Caretaker Root', FALSE, NULL, tx_caretaker_Constants::nodeType_Root, $hidden);
	}

	/**
	 * Get the caretaker node id of this node
	 * @return string
	 */
	public function getCaretakerNodeId() {
		return 'root';
	}

	/**
	 * Find Child nodes
	 * @param boolean $show_hidden
	 * @return array
	 * @see caretaker/trunk/classes/nodes/tx_caretaker_AggregatorNode#findChildren()
	 */
	protected function findChildren($show_hidden = FALSE) {
		$node_repository = tx_caretaker_NodeRepository::getInstance();
		$root_instancegroups = $node_repository->getInstancegroupsByParentGroupUid(0, $this, $show_hidden);
		$root_instances = $node_repository->getInstancesByInstancegroupUid(0, $this, $show_hidden);
		return array_merge($root_instancegroups, $root_instances);
	}

	/**
	 * Find Parent Node
	 * @return tx_caretaker_AbstractNode
	 */
	protected function findParent() {
		return FALSE;
	}

	/**
	 * @param int $testUid
	 * @return bool
	 */
	public function getTestConfigurationOverlayForTestUid($testUid) {
		return FALSE;
	}

}

?>