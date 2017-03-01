<?php

$extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
$advancedNotificationsEnabled = $extConfig['notifications.']['advanced.']['enabled'] == '1';

if ($advancedNotificationsEnabled) {
    $GLOBALS['TCA']['tx_caretaker_node_strategy_mm'] = [
        'ctrl' => [
            'hideTable' => 1,
            'label' => 'uid_strategy',
            'iconfile' => 'EXT:caretaker/res/icons/nodeaddressrelation.png',
        ],
        'interface' => [
            'showRecordFieldList' => '',
        ],
        'columns' => [
            'uid_strategy' => [
                'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_strategies',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'foreign_table' => 'tx_caretaker_strategies',
                ],
            ],
        ],
        'types' => [
            '0' => ['showitem' => 'uid_strategy;;1;;1-1-1'],
        ],
    ];
}
