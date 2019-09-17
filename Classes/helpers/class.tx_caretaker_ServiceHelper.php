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
 */
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class tx_caretaker_ServiceHelper
{
    /**
     * @var array
     */
    protected static $tcaTestServiceItems = array();

    /**
     * @var array
     */
    protected static $tcaTestConfigDs = array('default' => 'FILE:EXT:caretaker/Classes/services/tests/ds.tx_caretaker_default.xml');

    /**
     * @var array
     */
    protected static $tcaExitPointServiceItems = array();

    /**
     * @var array
     */
    protected static $tcaExitPointConfigDs = array(
        'default' => 'FILE:EXT:caretaker/Classes/services/tests/ds.tx_caretaker_default.xml',
    );

    /**
     * Array of all active Notification Services
     *
     * @var array
     */
    private static $notificationServiceInstances;

    /**
     * Returns an array with all services with the type "caretaker_test_service"
     *
     * @return array
     */
    public static function getAllCaretakerTestServices()
    {
        return $GLOBALS['T3_SERVICES']['caretaker_test_service'];
    }

    /**
     * Adds a service for caretaker. The service is registered and the type and flexform is added to the testconf
     *
     * @param string $extKey kex of the extension which is adding the service
     * @param string $path path to the flexform and service class without slashes before and after
     * @param string $key key which is used for to identify the service
     * @param string $title title of the testservice
     * @param string $description description of the testservice
     */
    public static function registerCaretakerService($extKey, $path, $key, $title, $description = '')
    {
        self::registerCaretakerTestService($extKey, $path, $key, $title, $description);
    }

    /**
     * Adds a service for caretaker. The service is registered and the type and flexform is added to the testconf
     *
     * @param string $extKey kex of the extension wich is adding the service
     * @param string $path path to the flexform and service class without slahes before and after
     * @param string $key key wich is used for to identify the service
     * @param string $title title of the testservice
     * @param string $description description of the testservice
     */
    public static function registerCaretakerTestService($extKey, $path, $key, $title, $description = '')
    {
        // load deferred registered test services from EXT:caretaker_instance, if that was loaded before EXT:caretaker
        if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('caretaker_instance')
            && class_exists('tx_caretakerinstance_ServiceHelper')
            && count(tx_caretakerinstance_ServiceHelper::$deferredTestServicesToRegister) > 0
        ) {
            $servicesToRegister = tx_caretakerinstance_ServiceHelper::$deferredTestServicesToRegister;
            tx_caretakerinstance_ServiceHelper::$deferredTestServicesToRegister = array();
            foreach ($servicesToRegister as $service) {
                self::registerCaretakerTestService($service[0], $service[1], $service[2], $service[3], $service[4]);
            }
        }

        if (!$GLOBALS['T3_SERVICES']['caretaker_test_service'][$key]) {
            // Register test service
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
                'caretaker',
                'caretaker_test_service',
                $key,
                array(
                    'title' => $title,
                    'description' => $description,
                    'subtype' => $key,
                    'available' => true,
                    'priority' => 50,
                    'quality' => 50,
                    'os' => '',
                    'exec' => '',
                    'classFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extKey) . $path . '/class.' . $key . 'TestService.php',
                    'className' => $key . 'TestService',
                )
            );

            // Add testtype to TCA
            self::$tcaTestServiceItems[] = array($title, $key);

            // Add flexform to service-item
            self::$tcaTestConfigDs[$key] = 'FILE:EXT:' . $extKey . '/' . $path . '/' . 'ds.' . $key . 'TestService.xml';
        }
    }

    /**
     * @return array
     */
    public static function getTcaTestServiceItems()
    {
        return self::$tcaTestServiceItems;
    }

    /**
     * @return array
     */
    public static function getTcaTestConfigDs()
    {
        return self::$tcaTestConfigDs;
    }

    public static function getTcaTestConfigDsWithIds()
    {
        $dsArray = array(
            'default' => self::$tcaTestConfigDs['default'],
        );
        if (version_compare(TYPO3_version, '8.0', '<') && isset($GLOBALS['TYPO3_DB'])) {
            $tests = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'tx_caretaker_test', 'deleted=0');
        } else {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_caretaker_test');
            try {
                $tests = $queryBuilder->select('*')
                    ->from('tx_caretaker_test')
                    ->where($queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter('0')))
                    ->execute();
            } catch (Exception $exception) {
            }
        }
        if (!empty($tests)) {
            foreach ($tests as $testRecord) {
                if (array_key_exists($testRecord['test_service'], self::$tcaTestConfigDs)) {
                    $dsArray[$testRecord['uid']] = self::$tcaTestConfigDs[$testRecord['test_service']];
                }
            }
        }

        return $dsArray;
    }

    /**
     * @return array
     */
    public static function getTcaExitPointServiceItems()
    {
        return self::$tcaExitPointServiceItems;
    }

    /**
     * @return array
     */
    public static function getTcaExitPointConfigDs()
    {
        return self::$tcaExitPointConfigDs;
    }

    /**
     * Register a new caretaker notification service. The ClassFile and
     *
     * @param string $extKey key of the extension wich is adding the service
     * @param string $serviceKey key which is used for the service
     * @param string $classPath path and filename of the php which implements the service
     * @param string $className the classname of the php-class which implements the service
     */
    public static function registerCaretakerNotificationService($extKey, $serviceKey, $classPath, $className)
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['caretaker']['notificationServices'][$serviceKey] = 'EXT:' . $extKey . '/' . $classPath . ':' . $className;
    }

    /**
     * Unregister a caretaker notification service.
     *
     * @param string $serviceKey key wich is used for the service
     */
    public static function unregisterCaretakerNotificationService($serviceKey)
    {
        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['caretaker']['notificationServices'][$serviceKey])) {
            unset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['caretaker']['notificationServices'][$serviceKey]);
        }
    }

    /**
     * Returns an array with all services with the type "caretaker_notification_service"
     *
     * @return array
     */
    public static function getAllCaretakerNotificationServices()
    {
        if (!self::$notificationServiceInstances) {
            self::loadAllCaretakerNotificationServices();
        }

        return self::$notificationServiceInstances;
    }

    /**
     * Get a specific notificationService
     *
     * @param string $serviceKey the notificationService key to get
     * @return tx_caretaker_NotificationServiceInterface|bool
     */
    public static function getCaretakerNotificationService($serviceKey)
    {
        if (!self::$notificationServiceInstances) {
            self::loadAllCaretakerNotificationServices();
        }
        if (self::$notificationServiceInstances[$serviceKey]) {
            return self::$notificationServiceInstances[$serviceKey];
        }
        return false;
    }

    /**
     * Load all active notificationServices into static array which are active and of correct type
     */
    protected static function loadAllCaretakerNotificationServices()
    {
        self::$notificationServiceInstances = array();
        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['caretaker']['notificationServices'] as $serviceKey => $notificationService) {
            $instance = \TYPO3\CMS\Core\Utility\GeneralUtility::getUserObj($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['caretaker']['notificationServices'][$serviceKey]);
            if ($instance instanceof tx_caretaker_NotificationServiceInterface && $instance->isEnabled()) {
                self::$notificationServiceInstances[$serviceKey] = $instance;
            }
        }
    }

    /**
     * @param string $extKey
     * @param string $path
     * @param string $key
     * @param string $title
     * @param string $description
     */
    public static function registerNotificationExitPoint($extKey, $path, $key, $title, $description = '')
    {
        if (!$GLOBALS['T3_SERVICES']['caretaker_exitpoint'][$key]) {
            // Register test service
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
                'caretaker',
                'caretaker_exitpoint',
                $key,
                array(
                    'title' => $title,
                    'description' => $description,
                    'subtype' => $key,
                    'available' => true,
                    'priority' => 50,
                    'quality' => 50,
                    'os' => '',
                    'exec' => '',
                    'classFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extKey) . $path . '/class.' . $key . 'ExitPoint.php',
                    'className' => $key . 'ExitPoint',
                )
            );
            self::$tcaExitPointServiceItems[] = array($title, $key);
            self::$tcaExitPointConfigDs[$key] = 'FILE:EXT:' . $extKey . '/' . $path . '/' . 'ds.' . $key . 'ExitPoint.xml';
        }
    }

    /**
     * Register ExtJsPlugin Panel
     *
     * @param string $id
     * @param string $xtype
     * @param string $cssIncludes
     * @param string $jsIncludes
     * @param string $extKey
     */
    public static function registerExtJsBackendPanel($id, $xtype, $cssIncludes, $jsIncludes, $extKey)
    {
        if (!$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['caretaker']['extJsBackendPanels']) {
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['caretaker']['extJsBackendPanels'] = array();
        }

        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['caretaker']['extJsBackendPanels'][$id] = array(
            'extKey' => $extKey,
            'id' => $id,
            'xtype' => $xtype,
            'cssIncludes' => $cssIncludes,
            'jsIncludes' => $jsIncludes,
        );
    }
}
