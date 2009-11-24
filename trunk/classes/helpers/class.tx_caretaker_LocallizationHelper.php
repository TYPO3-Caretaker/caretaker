<?php

/**
 * A Herlper class for the handling of locallization strings in caretaker
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

					$lcObj  = t3lib_div::makeInstance('tslib_cObj');
					$result = $lcObj->TEXT( array( 'data' => $locallangString ) ) ;
					break;
				
				case 'BE':

					$locallangParts = explode (':',$locallangString);

					array_shift($locallangParts);

					$locallang_key   = array_pop( $locallangParts );
					$locallang_file  = implode( ':' , $locallangParts );

					$language_key  = $GLOBALS['BE_USER']->uc['lang'];
					$LANG = t3lib_div::makeInstance('language');
					$LANG->init($language_key);

					$result = $LANG->getLLL($locallang_key, $LANG->readLLfile(t3lib_div::getFileAbsFileName( $locallang_file )));
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
