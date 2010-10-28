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
 * Helper which provides methods for the localization of strings in the
 * frontend and the backend.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_LocallizationHelper {
	
    /**
	 * Translate a given string in the current language
	 *
	 * @param string $string
	 * @return string
	 */
	static function locallizeString( $locallangString ){

			// handler whole LLL String
		if ( strpos( $locallangString , 'LLL:' ) !== 0 ){
			$result = $locallangString;
		} else {
			switch (TYPO3_MODE){
				case 'FE':
					
					// FE
					if ( $GLOBALS['TSFE'] ){
						$lcObj  = t3lib_div::makeInstance('tslib_cObj');
						$result = $lcObj->TEXT( array( 'data' => $locallangString ) ) ;
					} 
					// eID
					else { 
						$LANG = t3lib_div::makeInstance('language');
						$LANG->init($language_key);
						$result = $LANG->getLLL($locallang_key, t3lib_div::readLLfile(t3lib_div::getFileAbsFileName( $locallang_file) , $LANG->lang, $LANG->charSet ) );
					}
					
					break;
					
				case 'BE':

					$locallangParts = explode (':',$locallangString);

					array_shift($locallangParts);

					$locallang_key   = array_pop( $locallangParts );
					$locallang_file  = implode( ':' , $locallangParts );

					$language_key  = $GLOBALS['BE_USER']->uc['lang'];
					$LANG = t3lib_div::makeInstance('language');
					$LANG->init($language_key);

					$result = $LANG->getLLL($locallang_key, t3lib_div::readLLfile(t3lib_div::getFileAbsFileName( $locallang_file) , $LANG->lang, $LANG->charSet ) );
					break;

				default :

					$result = $locallangString;
					break;

			}
		} 

		/// recursive call for {LLL:} parts	
		$result = preg_replace_callback  ( '/{(LLL:EXT:[^ ]+?:[^ ]+?)}/' ,  'tx_caretaker_LocallizationHelper::locallizeSubstring'  , $result );

		return $result;
	}

	public static function locallizeSubstring($context){
		return tx_caretaker_LocallizationHelper::locallizeString($context[1]);
	}
}
?>
