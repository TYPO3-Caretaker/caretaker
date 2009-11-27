<?php

/***************************************************************
* Copyright notice
*
* (c) 2009 by n@work GmbH and networkteam GmbH
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
 * The logger for the caretaker cli script.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */class tx_caretaker_CliLogger implements tx_caretaker_LoggerInterface {
	
	/**
	 * Silent Mode
	 * 
	 * @var boolean
	 */	
	private $silentMode = false;
	
	/**
	 * Set the SilentMode
	 * 
	 * @param $silent
	 */
    public function setSilentMode($silent){
    	$this->silentMode = $silent;
    }

    /**
     * (non-PHPdoc)
     * @see caretaker/trunk/interfaces/tx_caretaker_LoggerInterface#log()
     */
    public function log($msg){
    	if ($this->silentMode == false){
	    	echo($msg.chr(10));
			flush();
    	}
    }

}
?>