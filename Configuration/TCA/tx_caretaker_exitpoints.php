<?php

$extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
$advancedNotificationsEnabled = $extConfig['notifications.']['advanced.']['enabled'] == '1';

if ($advancedNotificationsEnabled) {
    $GLOBALS['TCA']['tx_caretaker_exitpoints'] = [
        'ctrl' => [
            'title' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints',
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
            'iconfile' => 'EXT:caretaker/res/icons/exitpoint.png',
            'requestUpdate' => 'service',
            'searchFields' => 'name, description',
        ],
        'interface' => [
            'showRecordFieldList' => 'hidden,id,name,description,service,config',
        ],
        'columns' => [
            'hidden' => [
                'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
                'config' => [
                    'type' => 'check',
                    'default' => '0',
                ],
            ],
            'id' => [
                'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints.id',
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'eval' => 'required,nospace,unique',
                ],
            ],
            'name' => [
                'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints.name',
                'config' => [
                    'type' => 'input',
                    'size' => '255',
                ],
            ],
            'description' => [
                'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints.description',
                'config' => [
                    'type' => 'text',
                    'cols' => '50',
                    'rows' => '5',
                ],
            ],
            'service' => [
                'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints.service',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'items' => array_merge(
                        [
                            0 => [
                                'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints.service.select_exitpoint',
                                '',
                            ],
                        ],
                        \tx_caretaker_ServiceHelper::getTcaExitPointServiceItems()
                    ),
                    'size' => 1,
                    'maxitems' => 1,
                ],
            ],
            'config' => [
                'displayCond' => 'FIELD:service:REQ:true',
                'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints.config',
                'config' => [
                    'type' => 'flex',
                    'ds_pointerField' => 'service',
                    'ds' => \tx_caretaker_ServiceHelper::getTcaExitPointConfigDs(),
                ],
            ],
        ],
        'types' => [
            '0' => ['showitem' => 'id;;;;1-1-1, name, description, service, config'],
        ],
        'palettes' => [
            '1' => ['showitem' => 'hidden'],
        ],
    ];
}
