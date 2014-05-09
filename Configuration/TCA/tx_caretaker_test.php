<?php

return array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test',
		'label'     => 'title',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',
		'delete' => 'deleted',
		'requestUpdate' => 'test_service',
		'dividers2tabs'=> 1,
		'enablecolumns' => array (
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'iconfile'          => t3lib_extMgm::extRelPath('caretaker').'res/icons/test.png',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => '',
	),
	'interface' => array (
		'showRecordFieldList' => 'hidden,starttime,endtime,fe_group,title,description,test_interval,test_interval_start_hour,test_interval_stop_hour,test_service,test_conf,test_retry,test_due,roles,groups,instances'
	),
	'columns' => Array (
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
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.title',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'description' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.description',
			'config' => Array (
				'type' => 'text',
				'cols' => '50',
				'rows' => '5',
			),
			'defaultExtras' => 'richtext'
		),
		'test_interval' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_interval',
			'config' => Array (
				'type'     => 'select',
				'items'    =>Array(
					Array('always',          0),
					Array('1 Minute',       60),
					Array('5 Minutes',     300),
					Array('10 Minutes',    600),
					Array('15 Minutes',    900),
					Array('20 Minutes',   1200),
					Array('30 Minutes',   1800),
					Array('45 Minutes',   2700),
					Array('1 Hour',       3600),
					Array('2 Hours',      7200),
					Array('4 Hours',     14400),
					Array('8 Hours',     28800),
					Array('10 Hours',    36000),
					Array('12 Hours',    43200),
					Array('1 Day',       86400),
					Array('2 Days',     172800),
					Array('3 Days',     259200),
					Array('4 Days',     345600),
					Array('5 Days',     432000),
					Array('6 Days',     518400),
					Array('1 Week',     604800),
					Array('2 Weeks',   1209600),
					Array('4 Weeks',   2419200),
				),
				'default' => 0
			)
		),
		'test_interval_start_hour' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_interval_start_hour',
			'config' => Array (
				'type'     => 'select',
				'items'    =>Array(
					Array('',0),Array(1,1),Array(2,2),Array(3,3),Array(4,4),	Array(5,5),Array(6,6),Array(7,7),Array(8,8),Array(9,9),Array(10,10),Array(11,11),Array(12,12),
					Array(13,13),Array(14,14),Array(15,15),Array(16,16),Array(17,17),Array(18,18),Array(19,19),Array(20,20),Array(21,21),Array(22,22),Array(23,23),Array(24,24),
				)
			)
		),
		'test_interval_stop_hour' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_interval_stop_hour',
			'config' => Array (
				'type'     => 'select',
				'items'    =>Array(
					Array('',0),Array(1,1),Array(2,2),Array(3,3),Array(4,4),	Array(5,5),Array(6,6),Array(7,7),Array(8,8),Array(9,9),Array(10,10),Array(11,11),Array(12,12),
					Array(13,13),Array(14,14),Array(15,15),Array(16,16),Array(17,17),Array(18,18),Array(19,19),Array(20,20),Array(21,21),Array(22,22),Array(23,23),Array(24,24),
				)
			)
		),
		'test_service' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_service',
			'config' => Array (
				'type' => 'select',
				'items' => array (
					0 => array('LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_service.select_service', '')
				),
				'size' => 1,
				'maxitems' => 1,
			)
		),
		'test_conf' => Array (
			'displayCond' => 'FIELD:test_service:REQ:true',
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_conf',
			'config' => Array (
				'type' => 'flex',
				'ds_pointerField' => 'test_service',
				'ds' => array()
			)
		),
		'test_retry' =>Array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_retry',
			'config' => Array (
				'type'          => 'select',
				'items' => array (
					0 => array('LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_retry_0', 0),
					2 => array('LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_retry_1', 1),
					3 => array('LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_retry_2', 2),
					4 => array('LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_retry_3', 3),
					5 => array('LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_retry_4', 4),
					6 => array('LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_retry_5', 5),
				),
				'size' => 1,
				'maxitems' => 1,
				'default' => 0
			)
		),
		'test_due' =>Array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_due',
			'config' => Array (
				'type'          => 'check'
			)
		),
		'roles' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.roles',
			'config' => array (
				'type' => 'select',
				'foreign_table' => 'tx_caretaker_roles',
				'size' => 5,
				'autoSizeMax' => 25,
				'minitems' => 0,
				'maxitems' => 100,
				'MM' => 'tx_caretaker_test_roles_mm'
			)
		),
		'groups' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.groups',
			'config' => Array (
				'type'          => 'select',
				'foreign_table' => 'tx_caretaker_testgroup',
				'size'          => 5,
				'autoSizeMax'   => 25,
				'minitems'      => 0,
				'maxitems'      => 50,
				'MM'            => 'tx_caretaker_testgroup_test_mm',
			),

		),
		'instances' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.instances',
			'config' => Array (
				'type'          => 'select',
				'foreign_table' => 'tx_caretaker_instance',
				'size'          => 5,
				'autoSizeMax'   => 25,
				'minitems'      => 0,
				'maxitems'      => 10000,
				'MM'            => 'tx_caretaker_instance_test_mm',
			)
		)
	),
	'types' => array (
		'0' => array('showitem' => 'test_service;;;;1-1-1, hidden;;1;;2-2-2, title;;3;;3-3-3,test_interval;;2,test_retry, test_due, test_conf;;;;4-4-4,
					--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.tab.description, description,
					--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.tab.notifications, roles'
		)
	),
	'palettes' => array (
		'1' => array('showitem' => 'starttime,endtime,fe_group'),
		'2' => array('showitem' => 'test_interval_start_hour,test_interval_stop_hour'),
		'3' => array('showitem' => 'groups,instances')
	)
);