<?php

$GLOBALS['TCA']['tx_caretaker_contactaddress'] = [
    'ctrl' => [
        'title' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_contactaddress',
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
        'dividers2tabs' => 1,
        'iconfile' => 'EXT:caretaker/res/icons/contactaddress.png',
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden,name,email,xmpp',
    ],
    'columns' => [
        'hidden' => [
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
        'name' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_contactaddress.name',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ],
        ],
        'email' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_contactaddress.email',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ],
        ],
        'xmpp' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_contactaddress.xmpp',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ],
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'id;;;;1-1-1, name, email, xmpp'],
    ],
    'palettes' => [
        '1' => ['showitem' => 'hidden'],
    ],
];
