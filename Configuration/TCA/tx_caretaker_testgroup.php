<?php

$GLOBALS['TCA']['tx_caretaker_testgroup'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',
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
		'searchFields' => 'title, description'
	),
	'interface' => array(
		'showRecordFieldList' => 'hidden,tests,name'
	),
	'columns' => array(
		'hidden' => Array(
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array(
				'type' => 'check',
				'default' => '0'
			),
		),
		'starttime' => Array(
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.starttime',
			'config' => Array(
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'default' => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => Array(
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.endtime',
			'config' => Array(
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0',
				'range' => Array(
					'upper' => mktime(0, 0, 0, 12, 31, 2020),
					'lower' => mktime(0, 0, 0, date('m') - 1, date('d'), date('Y'))
				)
			)
		),
		'fe_group' => Array(

			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.fe_group',
			'config' => Array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => Array(
					Array('', 0),
					Array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					Array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					Array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		'title' => Array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.title',
			'config' => Array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'description' => Array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.description',
			'config' => Array(
				'type' => 'text',
				'cols' => '50',
				'rows' => '5',
			),
			'defaultExtras' => 'richtext'
		),
		'parent_group' => Array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.parent_group',
			'config' => Array(
				'type' => 'select',
				'renderMode' => 'tree', // for old versions
				'renderType' => 'selectTree', // for 7.4 and higher
				'treeConfig' => array(
					'parentField' => 'parent_group',
					'appearance' => array(
						'showHeader' => TRUE
					),
				),
				'foreign_table' => 'tx_caretaker_testgroup',
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'instances' => Array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.instances',
			'config' => Array(
				'type' => 'select',
				'foreign_table' => 'tx_caretaker_instance',
				'MM' => 'tx_caretaker_instance_testgroup_mm',
				'MM_opposite_field' => 'group',
				'size' => 5,
				'autoSizeMax' => 25,
				'minitems' => 0,
				'maxitems' => 10000,
			)
		),
		'tests' => Array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.tests',
			'config' => Array(
				'type' => 'select',
				'renderType' => 'selectMultipleSideBySide',
				'foreign_table' => 'tx_caretaker_test',
				'MM' => 'tx_caretaker_testgroup_test_mm',
				'MM_opposite_field' => 'groups',
				'size' => 5,
				'autoSizeMax' => 25,
				'minitems' => 0,
				'maxitems' => 10000,
				'wizards' => Array(
					'_PADDING' => 1,
					'_VERTICAL' => 1,
					'edit' => Array(
						'type' => 'popup',
						'title' => 'Edit Test',
						'module' => array(
							'name' => 'wizard_edit'
						),
						'icon' => 'edit2.gif',
						'popup_onlyOpenIfSelected' => 1,
						'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
					),
					'add' => Array(
						'type' => 'script',
						'title' => 'Create new Test',
						'icon' => 'add.gif',
						'params' => Array(
							'table' => 'tx_caretaker_test',
							'pid' => '###CURRENT_PID###',
							'setValue' => 'prepend'
						),
						'module' => array(
							'name' => 'wizard_add'
						)
					),
				),
			)
		)
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title, parent_group;;;;3-3-3,
		--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.tab.description,description,
		--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.tab.relations,tests;;;;4-4-4,
		--palette--;Instances;instances
		')
	),
	'palettes' => array(
		'1' => array('showitem' => 'starttime,endtime,fe_group'),
		'instances' => array('showitem' => 'instances', 'isHiddenPalette' => TRUE)
	)
);
