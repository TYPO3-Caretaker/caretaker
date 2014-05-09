<?php

return array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup',
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
		'iconfile'          => t3lib_extMgm::extRelPath('caretaker').'res/icons/instancegroup.png',
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
					Array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					Array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					Array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'exclusiveKeys' => '-1,-2',
				'foreign_table' => 'fe_groups',
				'foreign_table_where' => 'ORDER BY fe_groups.title',
				'maxitems' => 20,
				'size' => 7
			)
		),
		'title' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup.title',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'description' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup.description',
			'config' => Array (
				'type' => 'text',
				'cols' => '50',
				'rows' => '5',
			),
			'defaultExtras' => 'richtext'
		),
		'parent_group'=>Array(
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup.parent_group',
			'config' => Array (
				'type'          => 'select',
				'form_type'     => 'user',
				'userFunc'      => 'tx_ttaddress_treeview->displayGroupTree',
				'treeView'      => 1,
				'foreign_table' => 'tx_caretaker_instancegroup',
				'size'          => 1,
				'autoSizeMax'   => 10,
				'minitems'      => 0,
				'maxitems'      => 2,

				'items' => Array (
					Array('LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup.parent_group.select', 0),
				),
			)
		),
		'contacts' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup.contacts',
			'config' => array (
				'type' => 'inline',
				'foreign_table' => 'tx_caretaker_node_address_mm',
				'foreign_field' => 'uid_node',
				'foreign_table_field' => 'node_table',
				'appearance' => array (
					'collapseAll' => true,
					'expandSingle' => true
				)
			)
		),
		'notification_strategies' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_general.notification_strategies',
			'config' => array (
				'type' => 'inline',
				'foreign_table' => 'tx_caretaker_node_strategy_mm',
				'foreign_field' => 'uid_node',
				'foreign_table_field' => 'node_table',
				'foreign_selector' => 'uid_strategy',
				'appearance' => array (
					'collapseAll' => true,
					'expandSingle' => true
				)
			)
		)
	),
	'types' => array (
		'0' => array('showitem' => 'title;;1;;1-1-1, parent_group;;;;2-2-2,' .
		'--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup.tab.description, description, ' .
		'--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup.tab.contacts, contacts, '.
		'--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup.tab.notifications, notification_strategies, '.
		'--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup.tab.access, hidden, starttime, endtime, fe_group'
		)
	),
	'palettes' => array ()
);