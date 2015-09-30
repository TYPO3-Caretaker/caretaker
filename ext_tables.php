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

if (!defined('TYPO3_MODE')) die ('Access denied.');

$extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
$advancedNotificationsEnabled = $extConfig['notifications.']['advanced.']['enabled'] == '1';

// register Records

$TCA['tx_caretaker_instancegroup'] = array(
		'ctrl' => array(
				'title' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup',
				'label' => 'title',
				'tstamp' => 'tstamp',
				'crdate' => 'crdate',
				'cruser_id' => 'cruser_id',
				'default_sortby' => 'ORDER BY title',
				'delete' => 'deleted',
				'rootLevel' => -1,
				'treeParentField' => 'parent_group',
				'enablecolumns' => array(
						'disabled' => 'hidden',
						'starttime' => 'starttime',
						'endtime' => 'endtime',
						'fe_group' => 'fe_group',
				),
				'dividers2tabs' => 1,
				'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'tca.php',
				'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'res/icons/instancegroup.png',
		),
		'feInterface' => array(
				'fe_admin_fieldList' => '',
		)
);

$TCA['tx_caretaker_instance'] = array(
		'ctrl' => array(
				'title' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance',
				'label' => 'title',
				'tstamp' => 'tstamp',
				'crdate' => 'crdate',
				'cruser_id' => 'cruser_id',
				'default_sortby' => 'ORDER BY title',
				'delete' => 'deleted',
				'rootLevel' => -1,
				'enablecolumns' => array(
						'disabled' => 'hidden',
						'starttime' => 'starttime',
						'endtime' => 'endtime',
						'fe_group' => 'fe_group',
				),
				'dividers2tabs' => 1,
				'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'tca.php',
				'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'res/icons/instance.png',
		),
		'feInterface' => array(
				'fe_admin_fieldList' => '',
		)
);


$TCA['tx_caretaker_testgroup'] = array(
		'ctrl' => array(
				'title' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup',
				'label' => 'title',
				'tstamp' => 'tstamp',
				'crdate' => 'crdate',
				'cruser_id' => 'cruser_id',
				'default_sortby' => 'ORDER BY title',
				'delete' => 'deleted',
				'rootLevel' => -1,
				'treeParentField' => 'parent_group',
				'enablecolumns' => array(
						'disabled' => 'hidden',
						'starttime' => 'starttime',
						'endtime' => 'endtime',
						'fe_group' => 'fe_group',
				),
				'dividers2tabs' => 1,
				'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'tca.php',
				'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'res/icons/group.png',
		),
		'feInterface' => array(
				'fe_admin_fieldList' => '',
		)
);

/*
 * Register contact related tables
 */

$TCA['tx_caretaker_roles'] = array(
		'ctrl' => array(
				'title' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_roles',
				'label' => 'name',
				'tstamp' => 'tstamp',
				'crdate' => 'crdate',
				'cruser_id' => 'cruser_id',
				'default_sortby' => 'ORDER BY name',
				'delete' => 'deleted',
				'rootLevel' => -1,
				'enablecolumns' => array(
						'disabled' => 'hidden',
				),
				'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'tca.php',
				'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'res/icons/role.png',
		),
		'feInterface' => array(
				'fe_admin_fieldList' => '',
		),
);

$TCA['tx_caretaker_node_address_mm'] = array(
		'ctrl' => array(
				'hideTable' => 1,

				'label' => 'uid_address',
				'label_alt' => 'role',
				'label_alt_force' => 1,

				'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'tca.php',
				'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'res/icons/nodeaddressrelation.png',
		),
		'feInterface' => array(
				'fe_admin_fieldList' => '',
		),
);


$TCA['tx_caretaker_contactaddress'] = array(
		'ctrl' => array(
				'title' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_contactaddress',
				'label' => 'name',
				'tstamp' => 'tstamp',
				'crdate' => 'crdate',
				'cruser_id' => 'cruser_id',
				'default_sortby' => 'ORDER BY name',
				'delete' => 'deleted',
				'rootLevel' => -1,
				'enablecolumns' => array(
						'disabled' => 'hidden',
				),
				'dividers2tabs' => 1,
				'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'tca.php',
				'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'res/icons/contactaddress.png',
		),
		'feInterface' => array(
				'fe_admin_fieldList' => '',
		)
);

if ($advancedNotificationsEnabled) {
	/*
	 * Register strategy tables
	 */

	$TCA['tx_caretaker_node_strategy_mm'] = array(
			'ctrl' => array(
					'hideTable' => 1,

					'label' => 'uid_strategy',

					'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'tca.php',
					'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'res/icons/nodeaddressrelation.png',
			),
			'feInterface' => array(
					'fe_admin_fieldList' => '',
			),
	);

	$TCA['tx_caretaker_exitpoints'] = array(
			'ctrl' => array(
					'title' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints',
					'label' => 'name',
					'tstamp' => 'tstamp',
					'crdate' => 'crdate',
					'cruser_id' => 'cruser_id',
					'default_sortby' => 'ORDER BY name',
					'delete' => 'deleted',
					'rootLevel' => -1,
					'enablecolumns' => array(
							'disabled' => 'hidden',
					),
					'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'tca.php',
					'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'res/icons/exitpoint.png',
					'requestUpdate' => 'service',
			),
			'feInterface' => array(
					'fe_admin_fieldList' => '',
			),
	);

	$TCA['tx_caretaker_strategies'] = array(
			'ctrl' => array(
					'title' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_strategies',
					'label' => 'name',
					'tstamp' => 'tstamp',
					'crdate' => 'crdate',
					'cruser_id' => 'cruser_id',
					'default_sortby' => 'ORDER BY name',
					'delete' => 'deleted',
					'rootLevel' => -1,
					'enablecolumns' => array(
							'disabled' => 'hidden',
					),
					'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'tca.php',
					'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'res/icons/strategy.png',
			),
			'feInterface' => array(
					'fe_admin_fieldList' => '',
			),
	);
}

$TCA['tx_caretaker_test'] = array(
		'ctrl' => array(
				'title' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test',
				'label' => 'title',
				'tstamp' => 'tstamp',
				'crdate' => 'crdate',
				'cruser_id' => 'cruser_id',
				'default_sortby' => 'ORDER BY title',
				'delete' => 'deleted',
				'rootLevel' => -1,
				'requestUpdate' => 'test_service',
				'dividers2tabs' => 1,
				'enablecolumns' => array(
						'disabled' => 'hidden',
						'starttime' => 'starttime',
						'endtime' => 'endtime',
						'fe_group' => 'fe_group',
				),
				'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'tca.php',
				'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'res/icons/test.png',
		),
		'feInterface' => array(
				'fe_admin_fieldList' => '',
		),
);

// add API-Key to fe_user record
$tempColumns = array(
		'tx_caretaker_api_key' => Array(
				'exclude' => 1,
				'label' => 'LLL:EXT:caretaker/locallang_db.xml:fe_users.tx_caretaker_api_key',
				'config' => Array(
						'type' => 'input'
				)
		),
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users', $tempColumns, 1);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('fe_users', 'tx_caretaker_api_key');

if ($advancedNotificationsEnabled && \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('tt_address')) {
	$tempColumns = array(
			'tx_caretaker_xmpp' => array(
					'exclude' => 0,
					'label' => 'LLL:EXT:caretaker/locallang_db.xml:tt_address.tx_caretaker_xmpp',
					'config' => array(
							'type' => 'input',
							'size' => '30',
					)
			),
	);

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_address', $tempColumns, 1);
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tt_address', 'tx_caretaker_xmpp;;;;1-1-1');
}


// overview
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY . '_pi_overview'] = 'layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_pi_overview'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($_EXTKEY . '_pi_overview', 'FILE:EXT:' . $_EXTKEY . '/pi_overview/flexform_ds.xml');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(Array('LLL:EXT:caretaker/locallang_db.xml:tt_content.list_type_pi_overview', $_EXTKEY . '_pi_overview'), 'list_type');
if (TYPO3_MODE == "BE") $TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_caretaker_pi_overview_wizicon"] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'pi_overview/class.tx_caretaker_pi_overview_wizicon.php';

// singleview
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY . '_pi_singleview'] = 'layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_pi_singleview'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($_EXTKEY . '_pi_singleview', 'FILE:EXT:' . $_EXTKEY . '/pi_singleview/flexform_ds.xml');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(Array('LLL:EXT:caretaker/locallang_db.xml:tt_content.list_type_pi_singleview', $_EXTKEY . '_pi_singleview'), 'list_type');
if (TYPO3_MODE == "BE") $TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_caretaker_pi_singleview_wizicon"] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'pi_singleview/class.tx_caretaker_pi_singleview_wizicon.php';

// graphreport
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY . '_pi_graphreport'] = 'layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_pi_graphreport'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($_EXTKEY . '_pi_graphreport', 'FILE:EXT:' . $_EXTKEY . '/pi_graphreport/flexform_ds.xml');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(Array('LLL:EXT:caretaker/locallang_db.xml:tt_content.list_type_pi_graphreport', $_EXTKEY . '_pi_graphreport'), 'list_type');
if (TYPO3_MODE == "BE") $TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_caretaker_pi_graphreport_wizicon"] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'pi_graphreport/class.tx_caretaker_pi_graphreport_wizicon.php';

// abstract
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY . '_pi_abstract'] = 'layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_pi_abstract'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($_EXTKEY . '_pi_abstract', 'FILE:EXT:' . $_EXTKEY . '/pi_abstract/flexform_ds.xml');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(Array('LLL:EXT:caretaker/locallang_db.xml:tt_content.list_type_pi_abstract', $_EXTKEY . '_pi_abstract'), 'list_type');
if (TYPO3_MODE == "BE") $TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_caretaker_pi_abstract_wizicon"] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'pi_abstract/class.tx_caretaker_pi_abstract_wizicon.php';

// register Extension TS templates
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'res/typoscript/plugin', 'Caretaker Plugin Template');
// register Extension TS templates
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'res/typoscript/page', 'Caretaker Page Template');

// Register Backend Modules
if (TYPO3_MODE == "BE") {

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule("txcaretakerNav", "", "", \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . "mod_nav/");
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule("txcaretakerNav", "txcaretakerOverview", "", \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . "mod_overview/");

	$caretaker_modconf = NULL;
	if (isset($TBE_MODULES['file'])) {
		$caretaker_modconf = $TBE_MODULES['txcaretakerNav'];
		unset($TBE_MODULES['txcaretakerNav']);
	}
	// move module after 'file'
	$temp_TBE_MODULES = array();
	foreach ($TBE_MODULES as $key => $value) {
		if ($key == 'file') {
			$temp_TBE_MODULES[$key] = $value;
			$temp_TBE_MODULES['txcaretakerNav'] = $caretaker_modconf;
		} else {
			$temp_TBE_MODULES[$key] = $value;
		}
	}
	$TBE_MODULES = $temp_TBE_MODULES;
}

require(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('caretaker') . '/ext_conf_include.php');
