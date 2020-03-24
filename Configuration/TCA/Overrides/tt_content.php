<?php
defined('TYPO3_MODE') or die();

// overview
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array(
    'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tt_content.list_type_pi_overview',
    $_EXTKEY . '_pi_overview',
), 'list_type');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY . '_pi_overview'] = 'layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_pi_overview'] = 'pi_flexform';

// singleview
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array(
    'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tt_content.list_type_pi_singleview',
    $_EXTKEY . '_pi_singleview',
), 'list_type');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY . '_pi_singleview'] = 'layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_pi_singleview'] = 'pi_flexform';

// graphreport
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array(
    'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tt_content.list_type_pi_graphreport',
    $_EXTKEY . '_pi_graphreport',
), 'list_type');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY . '_pi_graphreport'] = 'layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_pi_graphreport'] = 'pi_flexform';

// abstract
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array(
    'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tt_content.list_type_pi_abstract',
    $_EXTKEY . '_pi_abstract',
), 'list_type');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY . '_pi_abstract'] = 'layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_pi_abstract'] = 'pi_flexform';
