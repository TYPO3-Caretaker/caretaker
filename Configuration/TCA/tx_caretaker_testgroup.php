<?php

$GLOBALS['TCA']['tx_caretaker_testgroup'] = array(
    'ctrl' => array(
        'title' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_testgroup',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'sortby' => 'sorting',
        'delete' => 'deleted',
        'rootLevel' => -1,
        'treeParentField' => 'parent_group',
        'enablecolumns' => array(
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group',
        ),
        'dividers2tabs' => 1,
        'iconfile' => 'EXT:caretaker/res/icons/group.png',
        'searchFields' => 'title, description',
    ),
    'interface' => array(
        'showRecordFieldList' => 'hidden,tests,name',
    ),
    'columns' => array(
        'hidden' => array(
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => array(
                'type' => 'check',
                'default' => '0',
            ),
        ),
        'starttime' => array(
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => array(
                'type' => 'input',
                'size' => '8',
                'eval' => 'date',
                'default' => '0',
                'checkbox' => '0',
                'renderType' => 'inputDateTime',
            ),
        ),
        'endtime' => array(
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => array(
                'type' => 'input',
                'size' => '8',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0',
                'range' => array(
                    'upper' => mktime(0, 0, 0, 12, 31, 2020),
                    'lower' => mktime(0, 0, 0, date('m') - 1, date('d'), date('Y')),
                ),
                'renderType' => 'inputDateTime',
            ),
        ),
        'fe_group' => array(
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.fe_group',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => array(
                    array('', 0),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--'),
                ),
                'foreign_table' => 'fe_groups',
            ),
        ),
        'title' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_testgroup.title',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ),
        ),
        'description' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_testgroup.description',
            'config' => array(
                'type' => 'text',
                'cols' => '50',
                'rows' => '5',
                'enableRichtext' => true,
            ),
        ),
        'parent_group' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_testgroup.parent_group',
            'config' => array(
                'type' => 'select',
                'renderMode' => 'tree', // for old versions
                'renderType' => 'selectTree', // for 7.4 and higher
                'treeConfig' => array(
                    'parentField' => 'parent_group',
                    'appearance' => array(
                        'showHeader' => true,
                    ),
                ),
                'foreign_table' => 'tx_caretaker_testgroup',
                'foreign_table_where' => 'ORDER BY tx_caretaker_testgroup.sorting ASC',
                'minitems' => 0,
                'maxitems' => 1,
            ),
        ),
        'instances' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_testgroup.instances',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_caretaker_instance',
                'MM' => 'tx_caretaker_instance_testgroup_mm',
                'MM_opposite_field' => 'group',
                'size' => 5,
                'autoSizeMax' => 25,
                'minitems' => 0,
                'maxitems' => 10000,
            ),
        ),
        'tests' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_testgroup.tests',
            'config' => array(
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
                'fieldControl' => array(
                    'addRecord' => array(
                        'pid' => '###CURRENT_PID###',
                        'table' => 'tx_caretaker_test',
                        'title' => 'Create new Test',
                        'setValue' => 'prepend',
                    ),
                    'editPopup' => array(
                        'title' => 'Edit Test',
                        'windowOpenParameters' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
                    ),
                ),
            ),
        ),
    ),
    'types' => array(
        '0' => array(
            'showitem' => '
                hidden, 
                --palette--;;1, 
                title, 
                parent_group,
                --div--;LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_testgroup.tab.description,description,
		        --div--;LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_testgroup.tab.relations,tests
		    ',
        ),
    ),
    'palettes' => array(
        '1' => array('showitem' => 'starttime,endtime,fe_group'),
        'instances' => array('showitem' => 'instances', 'isHiddenPalette' => true),
    ),
);
