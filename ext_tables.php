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
