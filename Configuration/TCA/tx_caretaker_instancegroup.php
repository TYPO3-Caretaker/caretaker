<?php

$extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
$advancedNotificationsEnabled = $extConfig['notifications.']['advanced.']['enabled'] == '1';

$GLOBALS['TCA']['tx_caretaker_instancegroup'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'type' => '',
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
		'iconfile' => 'EXT:caretaker/res/icons/instancegroup.png',
		'searchFields' => 'title, description'
	),
	'interface' => array(
		'showRecordFieldList' => 'name,tests,description,parent_group,contacts,notification_strategies,starttime,endtime,hidden,fe_group'
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
				'renderType' => 'selectMultipleSideBySide',
				'items' => Array(
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
		'title' => Array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup.title',
			'config' => Array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim,required',
			)
		),
		'description' => Array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup.description',
			'config' => Array(
				'type' => 'text',
				'cols' => '50',
				'rows' => '5',
			),
			'defaultExtras' => 'richtext'
		),
		'parent_group' => Array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup.parent_group',
			'config' => Array(
				'type' => 'select',
				'renderMode' => 'tree',
				'renderType' => 'selectTree',
				'treeConfig' => array(
					'parentField' => 'parent_group',
					'appearance' => array(
						'showHeader' => TRUE
					)
				),
				'foreign_table' => 'tx_caretaker_instancegroup',
				'minitems' => 0,
				'maxitems' => 1
			)
		),
		'contacts' => array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup.contacts',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'tx_caretaker_node_address_mm',
				'foreign_field' => 'uid_node',
				'foreign_table_field' => 'node_table',
				'appearance' => array(
					'collapseAll' => TRUE,
					'expandSingle' => TRUE
				)
			)
		),
		'notification_strategies' => array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_general.notification_strategies',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'tx_caretaker_node_strategy_mm',
				'foreign_field' => 'uid_node',
				'foreign_table_field' => 'node_table',
				'foreign_selector' => 'uid_strategy',
				'appearance' => array(
					'collapseAll' => TRUE,
					'expandSingle' => TRUE
				)
			)
		)
	),
	'types' => array(
		'0' => array(
			'showitem' => 'title;;1;;1-1-1, parent_group;;;;2-2-2,' .
				'--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup.tab.description, description, ' .
				'--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup.tab.contacts, contacts, ' .
				($advancedNotificationsEnabled ? '--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup.tab.notifications, notification_strategies, ' : '') .
				'--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup.tab.access, hidden, starttime, endtime, fe_group'
		)
	),
	'palettes' => array()
);
