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
 * Helper which provides service methods for fast and convenient registration of
 * testServices.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_ServiceHelper {

	/**
	 * Array of all active Notification Services
	 * @var array
	 */
	private static $notificationServiceInstances;

	/**
	 * Returns an array with all services with the type "caretaker_test_service"
	 * 
	 * @return array
	 * @deprecated
	 */
	public static function getAllCaretakerServices() {
		return tx_caretaker_ServiceHelper::getAllCaretakerTestServices();
	}

	/**
	 * Returns an array with all services with the type "caretaker_test_service"
	 *
	 * @return array
	 */
	public static function getAllCaretakerTestServices(){
		return $GLOBALS['T3_SERVICES']['caretaker_test_service'];
	}

	/**
	 * Adds a service for caretaker. The service is registered and the type and flexform is added to the testconf
	 *
	 * @param string $extKey kex of the extension wich is adding the service
	 * @param string $path path to the flexform and service class without slahes before and after
	 * @param string $key key wich is used for to identify the service
	 * @param string $title  title of the testservice
	 * @param string $description description of the testservice
	 */
	public static function registerCaretakerService ($extKey, $path, $key, $title, $description = '') {
		return tx_caretaker_ServiceHelper::registerCaretakerTestService($extKey, $path, $key, $title, $description);
	}
	
	/**
	 * Adds a service for caretaker. The service is registered and the type and flexform is added to the testconf
	 *
	 * @param string $extKey kex of the extension wich is adding the service
	 * @param string $path path to the flexform and service class without slahes before and after
	 * @param string $key key wich is used for to identify the service
	 * @param string $title  title of the testservice
	 * @param string $description description of the testservice
	 */
	public static function registerCaretakerTestService ($extKey, $path, $key, $title, $description = '') {
		global $TCA;

		t3lib_div::loadTCA('tx_caretaker_test');

			// Register test service
		t3lib_extMgm::addService(
			'caretaker',
			'caretaker_test_service',
			$key,	
			array(
				'title' => $title,
				'description' => $description,
				'subtype' => $key,
				'available' => TRUE,
				'priority' => 50,
				'quality' => 50,
				'os' => '',
				'exec' => '',
				'classFile' => t3lib_extMgm::extPath($extKey) . $path . '/class.' . $key . 'TestService.php',
				'className' => $key.'TestService',
			)
		);

			// Add testtype to TCA 
		if (is_array($TCA['tx_caretaker_test']['columns']) && is_array($TCA['tx_caretaker_test']['columns']['test_service']['config']['items'])) {
			$TCA['tx_caretaker_test']['columns']['test_service']['config']['items'][] =  array( $title, $key);
		}

			// Add flexform to service-item
		if (is_array($TCA['tx_caretaker_test']['columns']) && is_array($TCA['tx_caretaker_test']['columns']['test_conf']['config']['ds'])) {
			$TCA['tx_caretaker_test']['columns']['test_conf']['config']['ds'][$key] = 'FILE:EXT:'.$extKey.'/'.$path.'/'.( $flexform ? $flexform:'ds.'.$key.'TestService.xml');
		}
	}

	/**
	 * Register a new caretaker notification service. The ClassFile and
	 *
	 * @param string $extKey key of the extension wich is adding the service
	 * @param string $serviceKey  key wich is used for the service
	 * @param string $classFile path and filename of the php which implements the service
	 * @param string $className the classname of the php-class which implements the service
	 */
	public static function registerCaretakerNotificationService ( $extKey, $serviceKey, $classPath, $className ){
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['caretaker']['notificationServices'][$serviceKey] = 'EXT:'.$extKey.'/'.$classPath.':'.$className;
	}

	/**
	 * Returns an array with all services with the type "caretaker_notification_service"
	 * 
	 * @return array
	 */
	public static function getAllCaretakerNotificationServices(){
		if (!self::$notificationServiceInstances)  self::loadAllCaretakerNotificationServices();
		return self::$notificationServiceInstances;
	}

	/**
	 * Get a specific notificationService
	 * 
	 * @param string $serviceKey the notificationService key to get
	 * @return mixed tx_caretaker_NotificationServiceInterfaceObject of false
	 */
	public static function getCaretakerNotificationService($serviceKey){
		if (!self::$notificationServiceInstances)  self::loadAllCaretakerNotificationServices();
		if (self::$notificationServiceInstances[$serviceKey] ){
			return self::$notificationServiceInstances[$serviceKey];
		} else {
			return false;
		}
	}

	/**
	 * Load all active notificationServices into static array which are active and of correct type
	 * 
	 * @return void
	 */
	protected static function loadAllCaretakerNotificationServices(){
		self::$notificationServiceInstances = Array();
		foreach ( $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['caretaker']['notificationServices'] as $serviceKey => $notificationService){
			$instance = t3lib_div::getUserObj($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['caretaker']['notificationServices'][$serviceKey]);
			if ( $instance instanceof tx_caretaker_NotificationServiceInterface && $instance->isEnabled() ){
				self::$notificationServiceInstances[$serviceKey] = $instance;
			} 
		}
	}

	public static function registerNotificationExitPoint($extKey, $path, $key, $title, $description='') {
		global $TCA;
		
		t3lib_div::loadTCA('tx_caretaker_exitpoints');

			// Register test service
		t3lib_extMgm::addService(
			'caretaker',
			'caretaker_exitpoint',
			$key,
			array(
				'title' => $title,
				'description' => $description,
				'subtype' => $key,
				'available' => TRUE,
				'priority' => 50,
				'quality' => 50,
				'os' => '',
				'exec' => '',
				'classFile' => t3lib_extMgm::extPath($extKey).$path.'/class.'.$key.'ExitPoint.php',
				'className' => $key.'ExitPoint',
			)
		);

			// Add exitpoint to TCA
		if (is_array($TCA['tx_caretaker_exitpoints']['columns']) && is_array($TCA['tx_caretaker_exitpoints']['columns']['service']['config']['items'])) {
			$TCA['tx_caretaker_exitpoints']['columns']['service']['config']['items'][] =  array( $title, $key);
		}

			// Add flexform to service-item
		if (is_array($TCA['tx_caretaker_exitpoints']['columns']) && is_array($TCA['tx_caretaker_exitpoints']['columns']['config']['config']['ds'])) {
			$TCA['tx_caretaker_exitpoints']['columns']['config']['config']['ds'][$key] = 'FILE:EXT:'.$extKey.'/'.$path.'/'.( $flexform ? $flexform:'ds.'.$key.'ExitPoint.xml');
		}
	}

	/**
	 * Register ExtJsPlugin Panel
	 * 
	 * @param string $extKey
	 * @param string $path
	 * @param string $classname
	 * @param string $order
	 */
	public static function registerExtJsBackendPanel ( $id, $xtype, $cssIncludes, $jsIncludes, $extKey ){
		if ( !$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['caretaker']['extJsBackendPanels']) {
			$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['caretaker']['extJsBackendPanels'] = array();
		}

		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['caretaker']['extJsBackendPanels'][$id] = array(
			'extKey'      => $extKey,
			'id'          => $id,
			'xtype'       => $xtype,
			'cssIncludes' => $cssIncludes,
			'jsIncludes'  => $jsIncludes
		);

		// order by ids
		
	}

}
?>