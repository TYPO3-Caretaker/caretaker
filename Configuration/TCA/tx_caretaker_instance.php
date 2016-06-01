<?php

use TYPO3\CMS\Core\Utility\VersionNumberUtility;

$extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
$advancedNotificationsEnabled = $extConfig['notifications.']['advanced.']['enabled'] == '1';
$enableNewConfigurationOverrides = $extConfig['features.']['newConfigurationOverrides.']['enabled'] == '1';
if(VersionNumberUtility::convertVersionNumberToInteger(VersionNumberUtility::getCurrentTypo3Version()) >= VersionNumberUtility::convertVersionNumberToInteger('7.5.0')) {
	// enable new configurations overrides automatically with 7.5 and later
	$enableNewConfigurationOverrides = TRUE;
}

$GLOBALS['TCA']['tx_caretaker_instance'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',
		'delete' => 'deleted',
		'rootLevel' => -1,
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'dividers2tabs' => 1,
		'iconfile' => 'EXT:caretaker/res/icons/instance.png',
		'searchFields' => 'title, description, url, host'
	),
	'interface' => array(
		'showRecordFieldList' => 'hidden,instancegroup,testgroups,tests,request_mode,encryption_mode,encryption_key,project_namename,project_manager,domain,additional_domains,contacts,server_type,server_provider,server_customer_id,server_other,cms_url,cms_admin,cms_pwd,cms_install_pwd,accesses,other,request_url,contacts'
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
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.title',
			'config' => Array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'description' => Array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.description',
			'config' => Array(
				'type' => 'text',
				'cols' => '50',
				'rows' => '5',
			),
			'defaultExtras' => 'richtext'
		),
		'url' => Array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.url',
			'config' => Array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'host' => Array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.host',
			'config' => Array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'groups' => Array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.groups',
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
				'foreign_table' => 'tx_caretaker_testgroup',
				'foreign_table_where' => 'ORDER BY tx_caretaker_testgroup.sorting ASC',
				'minitems' => 0,
				'maxitems' => 50,
				'MM' => 'tx_caretaker_instance_testgroup_mm',
			)
		),
		'tests' => Array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.tests',
			'config' => Array(
				'type' => 'select',
				'renderType' => 'selectMultipleSideBySide',
				'foreign_table' => 'tx_caretaker_test',
				'foreign_table_where' => 'ORDER BY tx_caretaker_test.title ASC',
				'MM' => 'tx_caretaker_instance_test_mm',
				'MM_opposite_field' => 'instances',
				'size' => 5,
				'autoSizeMax' => 10,
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
		),
		'public_key' => Array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.public_key',
			'displayCond' => 'EXT:caretaker_instance:LOADED:true',
			'config' => Array(
				'type' => 'input',
				'eval' => 'trim',
				'max' => '1024'
			)
		),
		'instancegroup' => Array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.instancegroup',
			'config' => Array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_caretaker_instancegroup',
				'foreign_table_where' => 'ORDER BY tx_caretaker_instancegroup.title ASC',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
				'items' => Array(
					Array('', '0'),
				),

			)
		),
		'testconfigurations' => array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_conf',
			'config' => array(
				'type' => 'flex',
				'ds' => array(
					'default' => 'FILE:EXT:caretaker/res/flexform/ds.tx_caretaker_instance_testconfiguration.xml'
				)
			)
		),
		'configuration_overrides' => array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_conf',
			'config' => Array(
				'type' => 'inline',
				'foreign_table' => 'tx_caretaker_instance_override',
				'foreign_field' => 'instance',
				'appearance' => array(
					'newRecordLinkAddTitle' => TRUE,
					'levelLinksPosition' => 'both',
					'useSortable' => TRUE,
					'enabledControls' => array(
						'info' => FALSE,
						'new' => TRUE,
						'dragdrop' => TRUE,
						'sort' => FALSE,
						'hide' => TRUE,
						'delete' => TRUE,
						'localize' => FALSE
					)
				),
				'behavior' => array(
					'enableCascadingDelete' => TRUE
				)
			)
		),
		'contacts' => array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.contacts',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'tx_caretaker_node_address_mm',
				'foreign_field' => 'uid_node',
				'foreign_table_field' => 'node_table',
				// 'foreign_selector' => 'uid_address',
				'appearance' => array(
					'collapseAll' => true,
					'expandSingle' => true
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
					'collapseAll' => true,
					'expandSingle' => true
				)
			)
		)
	),
	'types' => array(
		'0' => array('showitem' => 'title;;;;2-2-2, instancegroup, url;;;;3-3-3, host, public_key,' .
			'--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.tab.description, description, ' .
			'--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.tab.relations, groups, tests, ' .
			'--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.tab.contacts, contacts, ' .
			($advancedNotificationsEnabled ? '--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.tab.notifications, notification_strategies, ' : '') .
			'--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.tab.testconfigurations, ' . ($enableNewConfigurationOverrides ? 'configuration_overrides, ' : 'testconfigurations,') .
			'--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.tab.access, hidden, starttime, endtime, fe_group'
		)
	),
	'palettes' => array()
);
