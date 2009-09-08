<?php 
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Christopher Hlubek <hlubek@networkteam.com>
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

/**
 * A helper class to instantiate an extension manager object. Will be
 * used for extension list updates. 
 */
class tx_caretaker_ExtensionManagerHelper {
	/**
	 * Create an instance of the extension manager
	 * @return SC_mod_tools_em_index An extension manager instance
	 */
	protected static function createInstance() {
		require_once(PATH_typo3 . '/mod/tools/em/class.em_index.php');

		$extensionManager = t3lib_div::makeInstance('SC_mod_tools_em_index');
		$extensionManager->init();
		if (empty($extensionManager->MOD_SETTINGS['mirrorListURL'])) {
			$extensionManager->MOD_SETTINGS['mirrorListURL'] = $GLOBALS['TYPO3_CONF_VARS']['EXT']['em_mirrorListURL'];
		}
		$extension->MOD_SETTINGS['rep_url'] = 'http://typo3.org/fileadmin/ter/';
		return $extensionManager;
	}

	/**
	 * Update the current list of extensions
	 * @return string The result message of the operation (without HTML tags)
	 */
	public static function updateExtensionList() {
		$extensionManager = self::createInstance();
		$result = $extensionManager->fetchMetaData('extensions');
		return strip_tags($result);
	}
}
?>