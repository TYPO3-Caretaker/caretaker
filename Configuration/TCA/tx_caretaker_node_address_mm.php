<?php

$GLOBALS['TCA']['tx_caretaker_node_address_mm'] = [
    'ctrl' => [
        'hideTable' => 1,
        'label' => 'uid_address',
        'label_alt' => 'role',
        'label_alt_force' => 1,
        'iconfile' => 'EXT:caretaker/res/icons/nodeaddressrelation.png',
    ],
    'interface' => [
        'showRecordFieldList' => '',
    ],
    'columns' => [
        'uid_address' => [
            'label' => 'LLL:EXT:tt_address/locallang_tca.xml:tt_address',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('tt_address') ? 'tt_address' : 'tx_caretaker_contactaddress',
                'wizards' => [
                    '_PADDING' => 1,
                    '_VERTICAL' => 1,
                    'edit' => [
                        'type' => 'script',
                        'title' => 'Create new address',
                        'icon' => 'add.gif',
                        'params' => [
                            'table' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('tt_address') ? 'tt_address' : 'tx_caretaker_contactaddress',
                            'pid' => '0',
                            'setValue' => 'prepend',
                        ],
                        'module' => [
                            'name' => 'wizard_add',
                        ],
                    ],
                ],
            ],
        ],
        'role' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_roles',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_caretaker_roles',
                'items' => [
                    ['', 0],
                ],
            ],
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'uid_address;;1;;1-1-1, role'],
    ],
];
