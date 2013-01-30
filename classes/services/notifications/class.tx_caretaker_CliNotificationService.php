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
 * The CLI Testrunner Output Notification-Service
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_CliNotificationService extends tx_caretaker_AbstractNotificationService  {

	public function  __construct() {
		parent::__construct('cli');
	}

    /**
	 * Notify the service about a test status
	 *
	 * @param string $event
	 * @param tx_caretaker_AbstractNode $node
	 * @param tx_caretaker_TestResult $result
	 * @param tx_caretaKer_TestResult $lastResult
	 */
	public function addNotification($event, $node, $result = NULL, $lastResult = NULL) {
		if (TYPO3_cliKey === 'scheduler') return;

		$indent = $this->getCliIndentation($node);

		if ( is_a ($node, 'tx_caretaker_TestNode' )  ) {
			$infotext = $result->getLocallizedInfotext();
			$msg  = $indent.'--+ '.$node->getTitle().' ['.$node->getCaretakerNodeId().']';
			$msg  .= str_replace( chr(10), chr(10).$indent.'  | ' , chr(10).$infotext );
			$msg  .= chr(10).$indent.'  +-> '.$result->getLocallizedStateInfo().' ('.$event.')';
		} else {
			if ( $result == NULL ){
				$msg = $indent.'--+ '.$node->getTitle().' ['.$node->getCaretakerNodeId().']'.$infotext.' '.$event;
			} else {
				$msg = $indent.'  +-> '.$result->getLocallizedStateInfo().' '.$event.' ['.$node->getCaretakerNodeId().']';
			}
		}

		echo ( $msg.chr(10) );
		flush();
	}

	/**
	 * Get the prefix string for each line in the cli based on the current hirarchy depth
	 *
	 * @param tx_caretaker_AbstractNode $node
	 * @return string
	 */
	protected function getCliIndentation( $node ){
		$indentation = '';
		while ( $node && $node = $node->getParent() ){
			$indentation .= '  |';
		}
		return $indentation;
	}
}
?>
