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
if (TYPO3_MODE == 'BE') {
    $TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['cliKeys'][$_EXTKEY] = array(
        'EXT:' . $_EXTKEY . '/Classes/class.tx_caretaker_Cli.php',
        '_CLI_caretaker',
    );
}

// register Plugins
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43($_EXTKEY, 'pi_overview/class.tx_caretaker_pi_overview.php', '_pi_overview', 'list_type', 0);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43($_EXTKEY, 'pi_singleview/class.tx_caretaker_pi_singleview.php', '_pi_singleview', 'list_type', 0);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43($_EXTKEY, 'pi_graphreport/class.tx_caretaker_pi_graphreport.php', '_pi_graphreport', 'list_type', 0);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43($_EXTKEY, 'pi_abstract/class.tx_caretaker_pi_abstract.php', '_pi_abstract', 'list_type', 0);

// Add eID script for caretaker tree loader

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerAjaxHandler(
    'tx_caretaker::treeloader',
    'EXT:caretaker/Classes/ajax/class.tx_caretaker_TreeLoader.php:tx_caretaker_TreeLoader->ajaxLoadTree',
    false
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerAjaxHandler(
    'tx_caretaker::nodeinfo',
    'EXT:caretaker/Classes/ajax/class.tx_caretaker_NodeInfo.php:tx_caretaker_nodeinfo->ajaxGetNodeInfo',
    false
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerAjaxHandler(
    'tx_caretaker::noderefresh',
    'EXT:caretaker/Classes/ajax/class.tx_caretaker_NodeInfo.php:tx_caretaker_nodeinfo->ajaxRefreshNode',
    false
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerAjaxHandler(
    'tx_caretaker::nodegraph',
    'EXT:caretaker/Classes/ajax/class.tx_caretaker_NodeInfo.php:tx_caretaker_nodeinfo->ajaxGetNodeGraph',
    false
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerAjaxHandler(
    'tx_caretaker::nodelog',
    'EXT:caretaker/Classes/ajax/class.tx_caretaker_NodeInfo.php:tx_caretaker_nodeinfo->ajaxGetNodeLog',
    false
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerAjaxHandler(
    'tx_caretaker::nodeproblems',
    'EXT:caretaker/Classes/ajax/class.tx_caretaker_NodeInfo.php:tx_caretaker_nodeinfo->ajaxGetNodeProblems',
    false
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerAjaxHandler(
    'tx_caretaker::nodecontacts',
    'EXT:caretaker/Classes/ajax/class.tx_caretaker_NodeInfo.php:tx_caretaker_nodeinfo->ajaxGetNodeContacts',
    false
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerAjaxHandler(
    'tx_caretaker::nodeSetAck',
    'EXT:caretaker/Classes/ajax/class.tx_caretaker_NodeInfo.php:tx_caretaker_nodeinfo->ajaxNodeSetAck',
    false
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerAjaxHandler(
    'tx_caretaker::nodeSetDue',
    'EXT:caretaker/Classes/ajax/class.tx_caretaker_NodeInfo.php:tx_caretaker_nodeinfo->ajaxNodeSetDue',
    false
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerAjaxHandler(
    'tx_caretaker::getModuleUrl',
    'EXT:caretaker/Classes/ajax/class.tx_caretaker_Utility.php:tx_caretaker_Utility->getModuleUrl',
    false
);

$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tceforms.php']['getSingleFieldClass'][] = 'EXT:caretaker/Classes/hooks/class.tx_caretaker_hooks_tceforms_getSingleFieldClass.php:tx_caretaker_hooks_tceforms_getSingleFieldClass';

// Register scheduler tasks for caretaker testrunner
$TYPO3_CONF_VARS['SC_OPTIONS']['scheduler']['tasks'][\Caretaker\Caretaker\Task\TestRunnerTask::class] = array(
    'extension' => $_EXTKEY,
    'title' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf:testrunnerTask.name',
    'description' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf:testrunnerTask.description',
    'additionalFields' => \Caretaker\Caretaker\Task\TestRunnerAdditionalFieldProvider::class,
);

// Register scheduler tasks for caretaker typo3 version number update
$TYPO3_CONF_VARS['SC_OPTIONS']['scheduler']['tasks'][\Caretaker\Caretaker\Task\Typo3VersionNumbersUpdateTask::class] = array(
    'extension' => $_EXTKEY,
    'title' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf:typo3versionnumbersupdateTask.name',
    'description' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf:typo3versionnumbersupdateTask.description',
);

require(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('caretaker') . '/ext_conf_include.php');

// eid script
$extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
if ($extConfig['eid.']['enabled']) {
    $TYPO3_CONF_VARS['FE']['eID_include']['tx_caretaker'] = 'EXT:caretaker/Classes/eid/class.tx_caretaker_Eid.php';
}

// register migration command controller
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'Caretaker\\Caretaker\\Command\\MigrationCommandController';
