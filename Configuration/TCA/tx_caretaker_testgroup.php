<?php

$GLOBALS['TCA']['tx_caretaker_testgroup'] = [
    'ctrl' => [
        'title' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'sortby' => 'sorting',
        'delete' => 'deleted',
        'rootLevel' => -1,
        'treeParentField' => 'parent_group',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group',
        ],
        'dividers2tabs' => 1,
        'iconfile' => 'EXT:caretaker/res/icons/group.png',
        'searchFields' => 'title, description',
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden,tests,name',
    ],
    'columns' => [
        'hidden' => [
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
        'starttime' => [
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.starttime',
            'config' => [
                'type' => 'input',
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'default' => '0',
                'checkbox' => '0',
            ],
        ],
        'endtime' => [
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.endtime',
            'config' => [
                'type' => 'input',
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0',
                'range' => [
                    'upper' => mktime(0, 0, 0, 12, 31, 2020),
                    'lower' => mktime(0, 0, 0, date('m') - 1, date('d'), date('Y')),
                ],
            ],
        ],
        'fe_group' => [

            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.fe_group',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                    ['LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1],
                    ['LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2],
                    ['LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--'],
                ],
                'foreign_table' => 'fe_groups',
            ],
        ],
        'title' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.title',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ],
        ],
        'description' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.description',
            'config' => [
                'type' => 'text',
                'cols' => '50',
                'rows' => '5',
            ],
            'defaultExtras' => 'richtext',
        ],
        'parent_group' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.parent_group',
            'config' => [
                'type' => 'select',
                'renderMode' => 'tree', // for old versions
                'renderType' => 'selectTree', // for 7.4 and higher
                'treeConfig' => [
                    'parentField' => 'parent_group',
                    'appearance' => [
                        'showHeader' => true,
                    ],
                ],
                'foreign_table' => 'tx_caretaker_testgroup',
                'foreign_table_where' => 'ORDER BY tx_caretaker_testgroup.sorting ASC',
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'instances' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.instances',
            'config' => [
                'type' => 'select',
                'foreign_table' => 'tx_caretaker_instance',
                'MM' => 'tx_caretaker_instance_testgroup_mm',
                'MM_opposite_field' => 'group',
                'size' => 5,
                'autoSizeMax' => 25,
                'minitems' => 0,
                'maxitems' => 10000,
            ],
        ],
        'tests' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.tests',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_caretaker_test',
                'foreign_table_where' => 'ORDER BY tx_caretaker_test.title ASC',
                'MM' => 'tx_caretaker_testgroup_test_mm',
                'MM_opposite_field' => 'groups',
                'size' => 5,
                'autoSizeMax' => 25,
                'minitems' => 0,
                'maxitems' => 10000,
                'wizards' => [
                    '_PADDING' => 1,
                    '_VERTICAL' => 1,
                    'edit' => [
                        'type' => 'popup',
                        'title' => 'Edit Test',
                        'module' => [
                            'name' => 'wizard_edit',
                        ],
                        'icon' => 'edit2.gif',
                        'popup_onlyOpenIfSelected' => 1,
                        'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
                    ],
                    'add' => [
                        'type' => 'script',
                        'title' => 'Create new Test',
                        'icon' => 'add.gif',
                        'params' => [
                            'table' => 'tx_caretaker_test',
                            'pid' => '###CURRENT_PID###',
                            'setValue' => 'prepend',
                        ],
                        'module' => [
                            'name' => 'wizard_add',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'hidden;;1;;1-1-1, title, parent_group;;;;3-3-3,
		--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.tab.description,description,
		--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.tab.relations,tests;;;;4-4-4,
		--palette--;Instances;instances
		',
        ],
    ],
    'palettes' => [
        '1' => ['showitem' => 'starttime,endtime,fe_group'],
        'instances' => ['showitem' => 'instances', 'isHiddenPalette' => true],
    ],
];
