<?php

return array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup',
		'label'     => 'title',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',
		'delete' => 'deleted',
		'treeParentField' => 'parent_group',
		'enablecolumns' => array (
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'dividers2tabs'=> 1,
		'iconfile'          => t3lib_extMgm::extRelPath('caretaker').'res/icons/group.png',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => '',
	),
	'interface' => array (
		'showRecordFieldList' => 'hidden,tests,name'
	),
	'columns' => array (

		'hidden' => Array (
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config'  => Array (
				'type'    => 'check',
				'default' => '0'
			),
		),
		'starttime' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.starttime',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'default' => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.endtime',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0',
				'range' => Array (
					'upper' => mktime(0,0,0,12,31,2020),
					'lower' => mktime(0,0,0,date('m')-1,date('d'),date('Y'))
				)
			)
		),
		'fe_group' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.fe_group',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
					Array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					Array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					Array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		'title' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.title',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'description' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.description',
			'config' => Array (
				'type' => 'text',
				'cols' => '50',
				'rows' => '5',
			),
			'defaultExtras' => 'richtext'
		),
		'parent_group'=>Array(
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.parent_group',
			'config' => Array (
				'type'          => 'select',
				'form_type'     => 'user',
				'userFunc'      => 'tx_ttaddress_treeview->displayGroupTree',
				'treeView'      => 1,
				'foreign_table' => 'tx_caretaker_testgroup',
				'size'          => 1,
				'autoSizeMax'   => 10,
				'minitems'      => 0,
				'maxitems'      => 2,

				'items' => Array (
					Array('LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.parent_group.select', 0),
				),
			)
		),
		'instances'=>Array(
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.instances',
			'config' => Array (
				'type'          => 'select',
				'foreign_table' => 'tx_caretaker_instance',
				'MM'            => 'tx_caretaker_instance_testgroup_mm',
				'MM_opposite_field' => 'group',
				'size'          => 5,
				'autoSizeMax'   => 25,
				'minitems'      => 0,
				'maxitems'      => 10000,
			)
		),
		'tests' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.tests',
			'config' => Array (
				'type'          => 'select',
				'foreign_table' => 'tx_caretaker_test',
				'MM'            => 'tx_caretaker_testgroup_test_mm',
				'MM_opposite_field' => 'groups',
				'size'          => 5,
				'autoSizeMax'   => 25,
				'minitems'      => 0,
				'maxitems'      => 10000,
				'wizards' => Array(
					'_PADDING' => 1,
					'_VERTICAL' => 1,
					'edit' => Array(
						'type' => 'popup',
						'title' => 'Edit Test',
						'script' => 'wizard_edit.php',
						'icon' => 'edit2.gif',
						'popup_onlyOpenIfSelected' => 1,
						'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
					),
					'add' => Array(
						'type' => 'script',
						'title' => 'Create new Test',
						'icon' => 'add.gif',
						'params' => Array(
							'table'=>'tx_caretaker_test',
							'pid' => '###CURRENT_PID###',
							'setValue' => 'prepend'
						),
						'script' => 'wizard_add.php',
					),
				),
			)
		)
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;2;;2-2-2, parent_group;;;;3-3-3,
		--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.tab.description,description,
		--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.tab.relations,tests;;;;4-4-4
		')
	),
	'palettes' => array (
		'1' => array('showitem' => 'starttime,endtime,fe_group'),
		'2' => array('showitem' => 'instances')

	)
);