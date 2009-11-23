<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classtx_caretaker_LocallizationHelper
 *
 * @author martin
 */
class tx_caretaker_LocallizationHelper {
	
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
