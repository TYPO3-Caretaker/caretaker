<?php
/**
 * This is a file of the caretaker project.
 * Copyright 2008 by n@work Internet Informationssystem GmbH (www.work.de)
 * 
 * @Author	Thomas Hempel 		<thomas@work.de>
 * @Author	Martin Ficzel		<martin@work.de>
 * @Author	Tobias Liebig		<mail_typo3.org@etobi.de>
 * @Author	Christopher Hlubek 	<hlubek@networkteam.com>
 * @Author	Patrick Kollodzik	<patrick@work.de>  
 * 
 * $$Id: tca.php 46 2008-06-19 16:09:17Z martin $$
 */

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_caretaker_test'] = array (
	'ctrl' => $TCA['tx_caretaker_test']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,exec_interval,testservice,testconf,name,last_exec'
	),
	'feInterface' => $TCA['tx_caretaker_test']['feInterface'],
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
			)
		),
		'test_interval' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_interval',
			'config' => Array (
				'type'     => 'select',
				'items'    =>Array(
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
					Array('1 Week',     604800),
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
		'groups' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.groups',
		
			'config' => Array (
				'type'          => 'select',
				'form_type'     => 'user',
				'userFunc'      => 'tx_ttaddress_treeview->displayGroupTree',
				'treeView'      => 1,
				'foreign_table' => 'tx_caretaker_testgroup',
				'size'          => 5,
				'autoSizeMax'   => 25,
				'minitems'      => 0,
				'maxitems'      => 50,
				'MM'            => 'tx_caretaker_testgroup_test_mm',
			),
			
		),
		
	),
	'types' => array (
		'0' => array('showitem' => 'title;;1;;1-1-1, test_service;;;;2-2-2,test_interval;;2, test_conf;;;;3-3-3,
					 --div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.tab.relations, groups,
					 --div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.tab.description, description'
					)
	),
	'palettes' => array (
		'1' => array('showitem' => 'hidden, starttime,endtime,fe_group'),
		'2' => array('showitem' => 'test_interval_start_hour,test_interval_stop_hour'),
	)
);

$TCA["tx_caretaker_instance"] = array (
	"ctrl" => $TCA["tx_caretaker_instance"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "hidden,instancegroup,testgroups,tests,request_mode,encryption_mode,encryption_key,project_namename,project_manager,domain,additional_domains,contacts,server_type,server_provider,server_customer_id,server_other,cms_url,cms_admin,cms_pwd,cms_install_pwd,accesses,other,request_url"
	),
	"feInterface" => $TCA["tx_caretaker_instance"]["feInterface"],
	"columns" => array (
	
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
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.title',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'description' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.description',
			'config' => Array (		
				'type' => 'text',
				'cols' => '50',
				'rows' => '5',
			)
		),
		'url' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.url',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'host' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.host',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'ip' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.ip',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'groups' => Array (
	      'exclude' => 1,
	      'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.groups',
	      'config' => Array (
			'type'          => 'select',
			'form_type'     => 'user',
			'userFunc'      => 'tx_ttaddress_treeview->displayGroupTree',
			'treeView'      => 1,
			'foreign_table' => 'tx_caretaker_testgroup',
			'size'          => 5,
			'autoSizeMax'   => 25,
			'minitems'      => 0,
			'maxitems'      => 50,
			'MM'            => 'tx_caretaker_instance_testgroup_mm',
	      )
	    ),
		'public_key' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.public_key',
			'config' => Array (
				'type' => 'input',
				'size' => '32',
				'eval' => 'trim,nospace',
			)
		),
		'instancegroup'=> Array (
	      'exclude' => 1,
	      'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.groups',
	      'config' => Array (
			'type'          => 'select',
			'form_type'     => 'user',
			'userFunc'      => 'tx_ttaddress_treeview->displayGroupTree',
			'treeView'      => 1,
			'foreign_table' => 'tx_caretaker_instancegroup',
			'size'          => 5,
			'autoSizeMax'   => 25,
			'minitems'      => 0,
			'maxitems'      => 99,
			'MM'            => 'tx_caretaker_instance_instancegroup_mm',
	      )
	    ),
	),
	"types" => array (
		"0" => array("showitem" => 'title;;1, description;;;;1-1-1, instancegroup, url;;;;-2-2-2, host, ip, public_key,
									--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.tab.relations,groups
		')
	),
	'palettes' => array (
		'1' => array('showitem' => 'hidden,starttime,endtime,fe_group')
	)
);


$TCA['tx_caretaker_testgroup'] = array (
	'ctrl' => $TCA['tx_caretaker_testgroup']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,tests,name'
	),
	"feInterface" => $TCA["tx_caretaker_testgroup"]["feInterface"],
	"columns" => array (
	
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
			)
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
		'tests' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.tests',
			'config' => Array (		
				'type'          => 'inline',
				'foreign_table' => 'tx_caretaker_test',
				'MM'            => 'tx_caretaker_testgroup_test_mm',
				'MM_opposite_field' => 'group',
				'size'          => 5,
				'autoSizeMax'   => 10,
				'minitems'      => 0,
				'maxitems'      => 99,
				'appearance' => Array( 
					'collapseAll' => 1, 
					'expandSingle' => 1, 
	    			'newRecordLinkAddTitle' => 1,
	   				'newRecordLinkPosition' => 'both',
				    'useCombination' => 1, 
	    
				),
			)
			
		),
		'instances'=>Array(
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.instances',
			'config' => Array (
				/*		
				'type'          => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_caretaker_instance',
				*/
		
				'type'          => 'select',
				'foreign_table' => 'tx_caretaker_instance',
				'MM'            => 'tx_caretaker_instance_testgroup_mm',
				'MM_opposite_field' => 'group',
				'size'          => 5,
				'autoSizeMax'   => 10,
				'minitems'      => 0,
				'maxitems'      => 99,
			
 			)
		),
    ),
	"types" => array (
		"0" => array("showitem" => 'title;;1;;1-1-1, description,parent_group;;;;2-2-2,
		--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testgroup.tab.relations, instances;;;;3-3-3,tests;;;;4-4-4,
		'
    	)
	),
	'palettes' => array (
		'1' => array('showitem' => 'hidden,starttime,endtime,fe_group')
	)
);


$TCA['tx_caretaker_instancegroup'] = array (
	'ctrl' => $TCA['tx_caretaker_instancegroup']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,tests,name'
	),
	"feInterface" => $TCA["tx_caretaker_instancegroup"]["feInterface"],
	"columns" => array (
	
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
			)
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
		)
    ),
	"types" => array (
		"0" => array("showitem" => 'title;;1;;1-1-1, description,parent_group;;;;2-2-2')
	),
	'palettes' => array (
		'1' => array('showitem' => 'hidden,starttime,endtime,fe_group')
	)
);

$TCA['tx_caretaker_testresult'] = array (
	'ctrl' => $TCA['tx_caretaker_testresult']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'test_id,instance_id,status,info_short,info_long,resultxml,resultplain'
	),
	'feInterface' => $TCA['tx_caretaker_testresult']['feInterface'],
	'columns' => array (
		'test_uid' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testresult.test_uid',
			'config' => Array (
				'type'     => 'select',
				'items' => array (
					0 => array('',''),
				),
				'foreign_table'     => 'tx_caretaker_test',
				'size' => 1,
				'minitems'=> 1,
				'maxitems'=>1,
			)
		),
		'instance_uid' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testresult.instance_uid',
			'config' => Array (
				'type'     => 'select',
				'items' => array (
					0 => array('',''),
				),
				'foreign_table'     => 'tx_caretaker_instance',
				'size' => 1,
				'minitems'=> 1,
				'maxitems'=>1,
			)
		),
		'group_uid' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testresult.group_uid',
			'config' => Array (
				'type'     => 'select',
				'items' => array (
					0 => array('',''),
				),
				'foreign_table'     => 'tx_caretaker_testgroup',
				'size' => 1,
				'minitems'=> 1,
				'maxitems'=>1,
			)
		),
		'result_status' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testresult.result_status',
			'config' => Array (
				'type'     => 'select',
				'items' => array (
					0 => array('LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testresult.result_status.neutral', -1),
					1 => array('LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testresult.result_status.ok', 0),
					2 => array('LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testresult.result_status.warning', 1),
					3 => array('LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testresult.result_status.error', 2),
					4 => array('LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testresult.result_status.unknown', 3),
				),
				'default' => -1
			)
		),
		'result_value' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testresult.result_value',
			'config' => Array (
				'type' => 'input',
				'size' => 20,
			)
		),
		'result_msg' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testresult.result_msg',
			'config' => Array (
				'type'     => 'input',
				'size'     => '50',
			)
		),
		'result_data' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testresult.result_data',
			'config' => Array (
				'type'     => 'text',
			)
		),
		
	),
	'types' => array (
		'0' => array('showitem' => 'test_uid;;;;1-1-1, instance_uid, group_uid, result_status;;;;2-2-2, result_value, result_msg, result_data')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);


?>