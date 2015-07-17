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
 * Plugin 'Overview' for the 'user_overview' extension.
 */
class tx_caretaker_pi_overview extends tx_caretaker_pibase {
	var $prefixId = 'tx_caretaker_pi_overview';        // Same as class name
	var $scriptRelPath = 'pi_overview/class.tx_caretaker_pi_overview.php';    // Path to this script relative to the extension dir.
	var $extKey = 'caretaker';    // The extension key.


	function getContent() {
		$nodes = $this->getNodes();

		if (count($nodes) > 0) {
			$content = '';
			foreach ($nodes as $node) {
				$content .= $this->showNodeInfo($node);
			}
			return $content;
		} else {
			return 'no node ids found';
		}
	}

	function getNodes() {
		$this->pi_initPIflexForm();
		$node_ids = $this->pi_getFFValue($this->cObj->data['pi_flexform'], 'node_ids');

		$nodes = array();
		$ids = explode(chr(10), $node_ids);

		$node_repository = tx_caretaker_NodeRepository::getInstance();

		foreach ($ids as $id) {
			$node = $node_repository->id2node($id);
			if ($node) $nodes[] = $node;
		}

		return $nodes;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/pi_overview/class.tx_caretaker_pi_overview.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/pi_overview/class.tx_caretaker_pi_overview.php']);
}

?>