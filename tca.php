<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2009-2011 by n@work GmbH and networkteam GmbH
 *
 * All rights reserved
 *
 * This script is part of the Caretaker project. The Caretaker project
 * is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * This is a file of the caretaker project.
 * http://forge.typo3.org/projects/show/extension-caretaker
 *
 * Project sponsored by:
 * n@work GmbH - http://www.work.de
 * networkteam GmbH - http://www.networkteam.com/
 *
 * $Id$
 */

if (!defined('TYPO3_MODE')) die ('Access denied.');

$extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
$advancedNotificationsEnabled = $extConfig['notifications.']['advanced.']['enabled'] == '1';

$TCA['tx_caretaker_instancegroup'] = array(
		'ctrl' => $TCA['tx_caretaker_instancegroup']['ctrl'],
		'interface' => array(
				'showRecordFieldList' => 'hidden,tests,name'
		),
		'feInterface' => $TCA['tx_caretaker_instancegroup']['feInterface'],
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
								'eval' => 'trim',
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
								'form_type' => 'user',
								'userFunc' => 'tx_ttaddress_treeview->displayGroupTree',
								'treeView' => 1,
								'foreign_table' => 'tx_caretaker_instancegroup',
								'size' => 1,
								'autoSizeMax' => 10,
								'minitems' => 0,
								'maxitems' => 2,

								'items' => Array(
										Array('LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup.parent_group.select', 0),
								),
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
				'0' => array('showitem' => 'title;;1;;1-1-1, parent_group;;;;2-2-2,' .
						'--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup.tab.description, description, ' .
						'--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup.tab.contacts, contacts, ' .
						($advancedNotificationsEnabled ? '--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup.tab.notifications, notification_strategies, ' : '') .
						'--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instancegroup.tab.access, hidden, starttime, endtime, fe_group'
				)
		),
		'palettes' => array()
);

$TCA['tx_caretaker_instance'] = array(
		'ctrl' => $TCA['tx_caretaker_instance']['ctrl'],
		'interface' => array(
				'showRecordFieldList' => 'hidden,instancegroup,testgroups,tests,request_mode,encryption_mode,encryption_key,project_namename,project_manager,domain,additional_domains,contacts,server_type,server_provider,server_customer_id,server_other,cms_url,cms_admin,cms_pwd,cms_install_pwd,accesses,other,request_url,contacts'
		),
		'feInterface' => $TCA['tx_caretaker_instance']['feInterface'],
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
								'form_type' => 'user',
								'userFunc' => 'tx_ttaddress_treeview->displayGroupTree',
								'treeView' => 1,
								'foreign_table' => 'tx_caretaker_testgroup',
								'size' => 5,
								'autoSizeMax' => 25,
								'minitems' => 0,
								'maxitems' => 50,
								'MM' => 'tx_caretaker_instance_testgroup_mm',
						)
				),
				'tests' => Array(
						'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.tests',
						'config' => Array(
								'type' => 'select',
								'foreign_table' => 'tx_caretaker_test',
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
														'table' => 'tx_caretaker_test',
														'pid' => '###CURRENT_PID###',
														'setValue' => 'prepend'
												),
												'script' => 'wizard_add.php',
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
								'foreign_table' => 'tx_caretaker_instancegroup',
								'size' => 1,
								'minitems' => 0,
								'maxitems' => 1,
								'items' => Array(
										Array('', '0'),
								),

						)
				),
				'notifications' => Array(
						'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.notifications',
						'config' => Array(
								'type' => 'group',
								'internal_type' => 'db',
								'allowed' => 'tt_address',
								'size' => 5,
								'autoSizeMax' => 25,
								'minitems' => 0,
								'maxitems' => 50,
						),
				),
				'testconfigurations' => array(
						'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_conf',
						'config' => Array(
								'type' => 'flex',
								'ds' => array(
										'default' => 'FILE:EXT:caretaker/res/flexform/ds.tx_caretaker_instance_testconfiguration.xml'
								)
						),
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
						'--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.tab.testconfigurations, testconfigurations, ' .
						'--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.tab.access, hidden, starttime, endtime, fe_group'
				)
		),
		'palettes' => array()
);

$TCA['tx_caretaker_node_address_mm'] = array(
		'ctrl' => $TCA['tx_caretaker_node_address_mm']['ctrl'],
		'interface' => array(
				'showRecordFieldList' => ''
		),
		'columns' => array(
				'uid_address' => array(
						'label' => 'LLL:EXT:tt_address/locallang_tca.xml:tt_address',
						'config' => array(
								'type' => 'select',
								'foreign_table' => 'tt_address',
								'wizards' => Array(
										'_PADDING' => 1,
										'_VERTICAL' => 1,
										'edit' => Array(
												'type' => 'script',
												'title' => 'Create new address',
												'icon' => 'add.gif',
												'params' => Array(
														'table' => 'tt_address',
														'pid' => '0',
														'setValue' => 'prepend'
												),
												'script' => 'wizard_add.php'
										)
								)
						)
				),
				'role' => array(
						'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_roles',
						'config' => array(
								'type' => 'select',
								'foreign_table' => 'tx_caretaker_roles',
								'items' => array(
										Array('', 0),
								),
						)
				)
		),
		'types' => array(
				'0' => array('showitem' => 'uid_address;;1;;1-1-1, role')
		)
);

if ($advancedNotificationsEnabled) {
	$TCA['tx_caretaker_node_strategy_mm'] = array(
			'ctrl' => $TCA['tx_caretaker_node_strategy_mm']['ctrl'],
			'interface' => array(
					'showRecordFieldList' => ''
			),
			'columns' => array(
					'uid_strategy' => array(
							'label' => 'LLL:EXT:tt_address/locallang_tca.xml:tx_caretaker_strategies',
							'config' => array(
									'type' => 'select',
									'foreign_table' => 'tx_caretaker_strategies'
							)
					)
			),
			'types' => array(
					'0' => array('showitem' => 'uid_strategy;;1;;1-1-1')
			)
	);
}


$TCA['tx_caretaker_testgroup'] = array(
		'ctrl' => $TCA['tx_caretaker_testgroup']['ctrl'],
		'interface' => array(
				'showRecordFieldList' => 'hidden,tests,name'
		),
		'feInterface' => $TCA['tx_caretaker_testgroup']['feInterface'],
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
								'form_type' => 'user',
								'userFunc' => 'tx_ttaddress_treeview->displayGroupTree',
								'treeView' => 1,
								'foreign_table' => 'tx_caretaker_testgroup',
								'size' => 1,
								'autoSizeMax' => 10,
								'minitems' => 0,
								'maxitems' => 2,

								'items' => Array(
										Array('LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.parent_group.select', 0),
								),
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
														'table' => 'tx_caretaker_test',
														'pid' => '###CURRENT_PID###',
														'setValue' => 'prepend'
												),
												'script' => 'wizard_add.php',
										),
								),
						)
				)
		),
		'types' => array(
				'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;2;;2-2-2, parent_group;;;;3-3-3,
		--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.tab.description,description,
		--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.tab.relations,tests;;;;4-4-4
		')
		),
		'palettes' => array(
				'1' => array('showitem' => 'starttime,endtime,fe_group'),
				'2' => array('showitem' => 'instances')
		)
);


$TCA['tx_caretaker_test'] = array(
		'ctrl' => $TCA['tx_caretaker_test']['ctrl'],
		'interface' => array(
				'showRecordFieldList' => 'hidden,starttime,endtime,fe_group,title,description,test_interval,test_interval_start_hour,test_interval_stop_hour,test_service,test_conf,test_retry,test_due,roles,groups,instances'
		),
		'feInterface' => $TCA['tx_caretaker_test']['feInterface'],
		'columns' => Array(
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
						'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.title',
						'config' => Array(
								'type' => 'input',
								'size' => '30',
								'eval' => 'trim',
						)
				),
				'description' => Array(
						'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.description',
						'config' => Array(
								'type' => 'text',
								'cols' => '50',
								'rows' => '5',
						),
						'defaultExtras' => 'richtext'
				),
				'test_interval' => Array(
						'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_interval',
						'config' => Array(
								'type' => 'select',
								'items' => Array(
										Array('always', 0),
										Array('1 Minute', 60),
										Array('5 Minutes', 300),
										Array('10 Minutes', 600),
										Array('15 Minutes', 900),
										Array('20 Minutes', 1200),
										Array('30 Minutes', 1800),
										Array('45 Minutes', 2700),
										Array('1 Hour', 3600),
										Array('2 Hours', 7200),
										Array('4 Hours', 14400),
										Array('8 Hours', 28800),
										Array('10 Hours', 36000),
										Array('12 Hours', 43200),
										Array('1 Day', 86400),
										Array('2 Days', 172800),
										Array('3 Days', 259200),
										Array('4 Days', 345600),
										Array('5 Days', 432000),
										Array('6 Days', 518400),
										Array('1 Week', 604800),
										Array('2 Weeks', 1209600),
										Array('4 Weeks', 2419200),
								),
								'default' => 0
						)
				),
				'test_interval_start_hour' => Array(
						'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_interval_start_hour',
						'config' => Array(
								'type' => 'select',
								'items' => Array(
										Array('', 0), Array(1, 1), Array(2, 2), Array(3, 3), Array(4, 4), Array(5, 5), Array(6, 6), Array(7, 7), Array(8, 8), Array(9, 9), Array(10, 10), Array(11, 11), Array(12, 12),
										Array(13, 13), Array(14, 14), Array(15, 15), Array(16, 16), Array(17, 17), Array(18, 18), Array(19, 19), Array(20, 20), Array(21, 21), Array(22, 22), Array(23, 23), Array(24, 24),
								)
						)
				),
				'test_interval_stop_hour' => Array(
						'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_interval_stop_hour',
						'config' => Array(
								'type' => 'select',
								'items' => Array(
										Array('', 0), Array(1, 1), Array(2, 2), Array(3, 3), Array(4, 4), Array(5, 5), Array(6, 6), Array(7, 7), Array(8, 8), Array(9, 9), Array(10, 10), Array(11, 11), Array(12, 12),
										Array(13, 13), Array(14, 14), Array(15, 15), Array(16, 16), Array(17, 17), Array(18, 18), Array(19, 19), Array(20, 20), Array(21, 21), Array(22, 22), Array(23, 23), Array(24, 24),
								)
						)
				),
				'test_service' => Array(
						'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_service',
						'config' => Array(
								'type' => 'select',
								'items' => array_merge(
										array(0 => array('LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_service.select_service', '')),
										\tx_caretaker_ServiceHelper::getTcaTestServiceItems()
								),
								'size' => 1,
								'maxitems' => 1,
						)
				),
				'test_conf' => Array(
						'displayCond' => 'FIELD:test_service:REQ:true',
						'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_conf',
						'config' => Array(
								'type' => 'flex',
								'ds_pointerField' => 'test_service',
								'ds' => \tx_caretaker_ServiceHelper::getTcaTestConfigDs()
						)
				),
				'test_retry' => Array(
						'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_retry',
						'config' => Array(
								'type' => 'select',
								'items' => array(
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
				'test_due' => Array(
						'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_due',
						'config' => Array(
								'type' => 'check'
						)
				),
				'roles' => array(
						'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.roles',
						'config' => array(
								'type' => 'select',
								'foreign_table' => 'tx_caretaker_roles',
								'size' => 5,
								'autoSizeMax' => 25,
								'minitems' => 0,
								'maxitems' => 100,
								'MM' => 'tx_caretaker_test_roles_mm'
						)
				),
				'groups' => Array(
						'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.groups',
						'config' => Array(
								'type' => 'select',
								'foreign_table' => 'tx_caretaker_testgroup',
								'size' => 5,
								'autoSizeMax' => 25,
								'minitems' => 0,
								'maxitems' => 50,
								'MM' => 'tx_caretaker_testgroup_test_mm',
						),
				),
				'instances' => Array(
						'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.instances',
						'config' => Array(
								'type' => 'select',
								'foreign_table' => 'tx_caretaker_instance',
								'size' => 5,
								'autoSizeMax' => 25,
								'minitems' => 0,
								'maxitems' => 10000,
								'MM' => 'tx_caretaker_instance_test_mm',
						)
				)
		),
		'types' => array(
				'0' => array('showitem' => 'test_service;;;;1-1-1, hidden;;1;;2-2-2, title;;3;;3-3-3,test_interval;;2,test_retry, test_due, test_conf;;;;4-4-4,
					--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.tab.description, description,
					--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.tab.notifications, roles'
				)
		),
		'palettes' => array(
				'1' => array('showitem' => 'starttime,endtime,fe_group'),
				'2' => array('showitem' => 'test_interval_start_hour,test_interval_stop_hour'),
				'3' => array('showitem' => 'groups,instances')
		)
);

$TCA['tx_caretaker_roles'] = array(
		'ctrl' => $TCA['tx_caretaker_roles']['ctrl'],
		'interface' => array(
				'showRecordFieldList' => 'hidden,id,name'
		),
		'feInterface' => $TCA['tx_caretaker_roles']['feInterface'],
		'columns' => Array(
				'hidden' => Array(
						'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
						'config' => Array(
								'type' => 'check',
								'default' => '0'
						),
				),
				'id' => Array(
						'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_roles.id',
						'config' => Array(
								'type' => 'input',
								'size' => '30',
								'eval' => 'unique,trim',
						)
				),
				'name' => Array(
						'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_roles.name',
						'config' => Array(
								'type' => 'input',
								'size' => '30',
								'eval' => 'unique,trim',
						)
				),
				'description' => Array(
						'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_roles.description',
						'config' => Array(
								'type' => 'text',
								'cols' => '50',
								'rows' => '5',
						)
				)
		),
		'types' => array(
				'0' => array('showitem' => 'id;;;;1-1-1, name, description')
		),
		'palettes' => array(
				'1' => array('showitem' => 'hidden')
		)
);

if ($advancedNotificationsEnabled) {
	$TCA['tx_caretaker_exitpoints'] = array(
			'ctrl' => $TCA['tx_caretaker_exitpoints']['ctrl'],
			'interface' => array(
					'showRecordFieldList' => 'hidden,id,name,description,service,config'
			),
			'feInterface' => $TCA['tx_caretaker_exitpoints']['feInterface'],
			'columns' => Array(
					'hidden' => Array(
							'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
							'config' => Array(
									'type' => 'check',
									'default' => '0'
							),
					),
					'id' => array(
							'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints.id',
							'config' => array(
									'type' => 'input',
									'size' => 30,
									'eval' => 'required,nospace,unique'
							)
					),
					'name' => Array(
							'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints.name',
							'config' => Array(
									'type' => 'input',
									'size' => '255',
							)
					),
					'description' => Array(
							'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints.description',
							'config' => Array(
									'type' => 'text',
									'cols' => '50',
									'rows' => '5',
							)
					),
					'service' => array(
							'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints.service',
							'config' => Array(
									'type' => 'select',
									'items' => array(
											0 => array('LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints.service.select_exitpoint', '')
									),
									'size' => 1,
									'maxitems' => 1,
							)
					),
					'config' => array(
							'displayCond' => 'FIELD:service:REQ:true',
							'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints.config',
							'config' => Array(
									'type' => 'flex',
									'ds_pointerField' => 'service',
									'ds' => array()
							)
					)
			),
			'types' => array(
					'0' => array('showitem' => 'id;;;;1-1-1, name, description, service, config')
			),
			'palettes' => array(
					'1' => array('showitem' => 'hidden')
			)
	);

	$TCA['tx_caretaker_strategies'] = array(
			'ctrl' => $TCA['tx_caretaker_strategies']['ctrl'],
			'interface' => array(
					'showRecordFieldList' => 'hidden,id,name'
			),
			'feInterface' => $TCA['tx_caretaker_strategies']['feInterface'],
			'columns' => Array(
					'hidden' => Array(
							'label' => 'LLL:EXT:lang/locallang_general.php:LGL.disable',
							'config' => Array(
									'type' => 'check',
									'default' => '0'
							),
					),
					'name' => Array(
							'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_strategies.name',
							'config' => Array(
									'type' => 'input',
									'size' => '30',
									'eval' => 'unique,trim',
							)
					),
					'description' => Array(
							'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_strategies.description',
							'config' => Array(
									'type' => 'text',
									'cols' => '50',
									'rows' => '5',
							)
					),
					'config' => array(
							'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_strategies.config',
							'config' => Array(
									'type' => 'text',
									'cols' => 50,
									'rows' => 50
							)
					)
			),
			'types' => array(
					'0' => array('showitem' => 'hidden;;;;1-1-1, id;;;;1-1-1, name, description, config')
			),
			'palettes' => array(
					'1' => array()
			)
	);
}
