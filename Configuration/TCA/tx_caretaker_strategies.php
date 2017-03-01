<?php

$extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
$advancedNotificationsEnabled = $extConfig['notifications.']['advanced.']['enabled'] == '1';

if ($advancedNotificationsEnabled) {
    $GLOBALS['TCA']['tx_caretaker_strategies'] = [
        'ctrl' => [
            'title' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_strategies',
            'label' => 'name',
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'cruser_id' => 'cruser_id',
            'default_sortby' => 'ORDER BY name',
            'delete' => 'deleted',
            'rootLevel' => -1,
            'enablecolumns' => [
                'disabled' => 'hidden',
            ],
            'iconfile' => 'EXT:caretaker/res/icons/strategy.png',
            'searchFields' => 'name, description',
        ],
        'interface' => [
            'showRecordFieldList' => 'hidden,id,name',
        ],
        'columns' => [
            'hidden' => [
                'label' => 'LLL:EXT:lang/locallang_general.php:LGL.disable',
                'config' => [
                    'type' => 'check',
                    'default' => '0',
                ],
            ],
            'name' => [
                'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_strategies.name',
                'config' => [
                    'type' => 'input',
                    'size' => '30',
                    'eval' => 'unique,trim',
                ],
            ],
            'description' => [
                'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_strategies.description',
                'config' => [
                    'type' => 'text',
                    'cols' => '50',
                    'rows' => '5',
                ],
            ],
            'config' => [
                'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_strategies.config',
                'config' => [
                    'type' => 'text',
                    'cols' => 50,
                    'rows' => 50,
                ],
            ],
        ],
        'types' => [
            '0' => ['showitem' => 'hidden;;;;1-1-1, id;;;;1-1-1, name, description, config'],
        ],
        'palettes' => [
            '1' => [],
        ],
    ];
}
