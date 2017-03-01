<?php

$GLOBALS['TCA']['tx_caretaker_roles'] = [
    'ctrl' => [
        'title' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_roles',
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
        'iconfile' => 'EXT:caretaker/res/icons/role.png',
        'searchFields' => 'name, description',
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden,id,name',
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
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_roles.id',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'unique,trim',
            ],
        ],
        'name' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_roles.name',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'unique,trim',
            ],
        ],
        'description' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_roles.description',
            'config' => [
                'type' => 'text',
                'cols' => '50',
                'rows' => '5',
            ],
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'id;;;;1-1-1, name, description'],
    ],
    'palettes' => [
        '1' => ['showitem' => 'hidden'],
    ],
];
