<?php

$extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
$advancedNotificationsEnabled = $extConfig['notifications.']['advanced.']['enabled'] == '1';

if ($advancedNotificationsEnabled && \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('tt_address')) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
        'tt_address',
        array(
            'tx_caretaker_xmpp' => array(
                'exclude' => 0,
                'label' => 'LLL:EXT:caretaker/locallang_db.xml:tt_address.tx_caretaker_xmpp',
                'config' => array(
                    'type' => 'input',
                    'size' => '30',
                ),
            ),
        )
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tt_address', 'tx_caretaker_xmpp;;;;1-1-1');
}
