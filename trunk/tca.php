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
				'type'     => 'input',
				'size'     => '4',
				'max'      => '4',
				'eval'     => 'int',
				'checkbox' => '0',
				'range'    => Array (
					'upper' => '1000',
					'lower' => '10'
				),
				'default' => 0
			)
		),
		'test_service' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_service',
			'config' => Array (
				'type' => 'select',
				'items' => array (
					0 => array('LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_service.select_service', 0)
				),
				'itemsProcFunc' => 'EXT:caretaker/classes/class.tx_caretaker_befunctions.php:tx_caretaker_befunctions->serviceItems',
				'size' => 1,
				'maxitems' => 1,
			)
		),
		'test_mode' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_mode',
			'config' => Array (
				'type' => 'select',
				'items' => array (
					0 => array('LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_mode.instance', 'instance'),
					1 => array('LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_mode.group', 'group')
				),
				'size' => 1,
				'maxitems' => 1,
			)
		),
		'test_conf' => Array (
			'config' => Array (
				'type' => 'flex',
				'ds_pointerField' => 'test_service',
				'ds' => array()
			)
		),
		'instances' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.instances',
			'config' => Array (
				'type' => 'inline',
				'foreign_table'    => 'tx_caretaker_instance_test_rel',
	    		'foreign_field'    => 'test_id',
	    		'foreign_sortby'   => 'test_sorting',
	  			'foreign_label'    => 'instance_id',
	     		'foreign_selector' => 'instance_id',
	    		'foreign_unique'   => 'instance_id',
				'maxitems' => 99,
	    	    'autoSizeMax' => 25,
	    		'size' => 5, 
				'appearance' => Array( 
					'collapseAll' => 1, 
					'expandSingle' => 1, 
	    			'newRecordLinkAddTitle' => 1,
	   				'newRecordLinkPosition' => 'both',
				    'useCombination' => 1, 
	    
				), 	    
			)
		),
		'groups' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.groups',
			'config' => Array (
				'type' => 'inline',
				'foreign_table'    => 'tx_caretaker_group_test_rel',
	    		'foreign_field'    => 'test_id',
	    		'foreign_sortby'   => 'test_sorting',
	  			'foreign_label'    => 'group_id',
	     		'foreign_selector' => 'group_id',
	    		'foreign_unique'   => 'group_id',
				'maxitems' => 99,
	    	    'autoSizeMax' => 25,
	    		'size' => 5, 
				'appearance' => Array( 
					'collapseAll' => 1, 
					'expandSingle' => 1, 
	    			'newRecordLinkAddTitle' => 1,
	   				'newRecordLinkPosition' => 'both',
				    'useCombination' => 1, 
	    
				), 	    
			)
		),
		
	),
	'types' => array (
		'0' => array('showitem' => 'title;;1;;1-1-1, description,test_service;;;;2-2-2, test_mode, test_interval, --div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.tab.relations, instances, groups' ),
		'1' => array('showitem' => 'title;;1;;1-1-1, description,test_service;;;;2-2-2, test_mode, test_interval, test_conf, --div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.tab.relations, groups, instances')
	),
	'palettes' => array (
		'1' => array('showitem' => 'hidden, starttime,endtime,fe_group')
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
		'groups' => Array (
	      'exclude' => 1,
	      'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.groups',
	      'config' => Array (
			'type'          => 'select',
			'form_type'     => 'user',
			'userFunc'      => 'tx_ttaddress_treeview->displayGroupTree',
			'treeView'      => 1,
			'foreign_table' => 'tx_caretaker_group',
			'size'          => 5,
			'autoSizeMax'   => 25,
			'minitems'      => 0,
			'maxitems'      => 50,
			'MM'            => 'tx_caretaker_instance_group_mm',
	      )
	    ),
		'tests' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.tests',
			'config' => Array (
				'type' => 'inline',
				'foreign_table'    => 'tx_caretaker_instance_test_rel',
	    		'foreign_field'    => 'instance_id',
	    		'foreign_sortby'   => 'instance_sorting',
	  			'foreign_label'    => 'test_id',
	     		'foreign_selector' => 'test_id',
	    		'foreign_unique'   => 'test_id',
				'maxitems' => 99,
	    	    'autoSizeMax' => 25,
	    		'size' => 5, 
				'appearance' => Array( 
					'collapseAll' => 1, 
					'expandSingle' => 1, 
	    			'newRecordLinkAddTitle' => 1,
	   				'newRecordLinkPosition' => 'both',
				    'useCombination' => 1, 
	    
				), 	    
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
		'flexinfo' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.flexinfo',
			'config' => Array (
				'type' => 'flex',
				'ds'=> Array (
					'default' => 'FILE:EXT:caretaker/flexform_instance_flexinfo.xml', 
				)
			)
		),
	),
	"types" => array (
		"0" => array("showitem" => '
				title;;1, description, url, public_key,
				--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.tab.relations, groups, tests,
				--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.tab.infos, flexinfo
			'
		)
	),
	'palettes' => array (
		'1' => array('showitem' => 'hidden,starttime,endtime,fe_group')
	)
);

$TCA["tx_caretaker_instance_test_rel"] = array (
	"ctrl" => $TCA["tx_caretaker_instance_test_rel"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "hidden,instancegroup,testgroups,tests,request_mode,encryption_mode,encryption_key,project_namename,project_manager,domain,additional_domains,contacts,server_type,server_provider,server_customer_id,server_other,cms_url,cms_admin,cms_pwd,cms_install_pwd,accesses,other,request_url"
	),
	"feInterface" => $TCA["tx_caretaker_instance_test_rel"]["feInterface"],
	"columns" => array (
		'hidden' => Array (        
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config'  => Array (
				'type'    => 'check',
				'default' => '0'
			),
		),
		'instance_id' => array(
			'label'   => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance_test_rel.instance_id',
			'config'  => Array (
				'type'    => 'select',
				'foreign_table' => 'tx_caretaker_instance',
				'maxitems' => 1
			),
		),
		'test_id' => array(
			'label'   => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance_test_rel.test_id',
			'config'  => Array (
				'type'    => 'select',
				'foreign_table' => 'tx_caretaker_test',
				'maxitems' => 1
			),
		),
		'instance_sorting' => array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance_test_rel.instance_sorting',
			'type' => 'passthrough',
		),
		'test_sorting' => array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance_test_rel.test_sorting',
			'type' => 'passthrough',
		),
	),
	"types" => array (
		"0" => array("showitem" => 'hidden,instance_id,test_id')
	),
	'palettes' => array ()
);

$TCA['tx_caretaker_group'] = array (
	'ctrl' => $TCA['tx_caretaker_group']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,tests,name'
	),
	"feInterface" => $TCA["tx_caretaker_group"]["feInterface"],
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
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_group.title',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'description' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_group.description',
			'config' => Array (		
				'type' => 'text',
				'cols' => '50',
				'rows' => '5',
			)
		),
		'parent_group'=>Array(
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_group.parent_group',
			'config' => Array (		
				'type'          => 'select',
				'form_type'     => 'user',
				'userFunc'      => 'tx_ttaddress_treeview->displayGroupTree',
				'treeView'      => 1,
				'foreign_table' => 'tx_caretaker_group',
				'size'          => 1,
				'autoSizeMax'   => 10,
				'minitems'      => 0,
				'maxitems'      => 2,
		
				'items' => Array (
                    Array('LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_group.parent_group.select', 0),
                ),
			)
		),
		'tests' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.tests',
			'config' => Array (
				'type' => 'inline',
				'foreign_table'    => 'tx_caretaker_group_test_rel',
	    		'foreign_field'    => 'group_id',
	    		'foreign_sortby'   => 'group_sorting',
	  			'foreign_label'    => 'test_id',
	     		'foreign_selector' => 'test_id',
	    		'foreign_unique'   => 'test_id',
				'maxitems' => 99,
	    	    'autoSizeMax' => 25,
	    		'size' => 5, 
				'appearance' => Array( 
					'collapseAll' => 1, 
					'expandSingle' => 1, 
	    			'newRecordLinkAddTitle' => 1,
	   				'newRecordLinkPosition' => 'both',
				    'useCombination' => 1, 
	    
				), 	    
			)
		),
		 'flexinfo' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_group.flexinfo',
			'config' => Array (
				'type' => 'flex',
				'ds'=> Array (
					'default' => 'FILE:EXT:caretaker/flexform_group_flexinfo.xml', 
				)
			)
		),
    ),
	"types" => array (
		"0" => array("showitem" => 'title;;1;;1-1-1, description,parent_group,
		--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_group.tab.relations, tests,
		--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_group.tab.infos, flexinfo
		'
    	)
	),
	'palettes' => array (
		'1' => array('showitem' => 'hidden,starttime,endtime,fe_group')
	)
);

$TCA["tx_caretaker_group_test_rel"] = array (
	"ctrl" => $TCA["tx_caretaker_group_test_rel"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "hidden,instancegroup,testgroups,tests,request_mode,encryption_mode,encryption_key,project_namename,project_manager,domain,additional_domains,contacts,server_type,server_provider,server_customer_id,server_other,cms_url,cms_admin,cms_pwd,cms_install_pwd,accesses,other,request_url"
	),
	"feInterface" => $TCA["tx_caretaker_group_test_rel"]["feInterface"],
	"columns" => array (
		'hidden' => Array (        
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config'  => Array (
				'type'    => 'check',
				'default' => '0'
			),
		),
		'group_id' => array(
			'label'   => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_group.group_id',
			'config'  => Array (
				'type'    => 'select',
				'foreign_table' => 'tx_caretaker_group',
				'maxitems' => 1
			),
		),
		'test_id' => array(
			'label'   => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_group.test_id',
			'config'  => Array (
				'type'    => 'select',
				'foreign_table' => 'tx_caretaker_test',
				'maxitems' => 1
			),
		),
		'group_sorting' => array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_group.group_sorting',
			'type' => 'passthrough',
		),
		'test_sorting' => array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_group.test_sorting',
			'type' => 'passthrough',
		),
	),
	"types" => array (
		"0" => array("showitem" => 'hidden,group_id,test_id')
	),
	'palettes' => array ()
);

/*

$TCA['tx_caretaker_testresults'] = array (
	'ctrl' => $TCA['tx_caretaker_testresults']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'test_id,instance_id,status,info_short,info_long,resultxml,resultplain'
	),
	'feInterface' => $TCA['tx_caretaker_testresults']['feInterface'],
	'columns' => array (
		'test_id' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testresults.test_id',
			'config' => Array (
				'type'     => 'input',
				'size'     => '4',
				'max'      => '4',
				'eval'     => 'int',
				'checkbox' => '0',
				'range'    => Array (
					'upper' => '1000',
					'lower' => '10'
				),
				'default' => 0
			)
		),
		'instance_id' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testresults.instance_id',
			'config' => Array (
				'type'     => 'input',
				'size'     => '4',
				'max'      => '4',
				'eval'     => 'int',
				'checkbox' => '0',
				'range'    => Array (
					'upper' => '1000',
					'lower' => '10'
				),
				'default' => 0
			)
		),
		'status' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testresults.status',
			'config' => Array (
				'type'     => 'input',
				'size'     => '4',
				'max'      => '4',
				'eval'     => 'int',
				'checkbox' => '0',
				'range'    => Array (
					'upper' => '1000',
					'lower' => '10'
				),
				'default' => 0
			)
		),
		'info_short' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testresults.info_short',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'info_long' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testresults.info_long',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'resultxml' => Array (
			'config' => Array (
				'type' => 'passthrough',
			)
		),
		'resultplain' => Array (
			'config' => Array (
				'type' => 'passthrough',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'test_id;;;;1-1-1, instance_id, status, info_short, info_long, resultxml, resultplain')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
*/


$TCA['tx_caretaker_accounts'] = array (
    'ctrl' => $TCA['tx_caretaker_accounts']['ctrl'],
    'interface' => array (
        'showRecordFieldList' => 'hidden,protocol,username,password,url,description'
    ),
    'feInterface' => $TCA['tx_caretaker_accounts']['feInterface'],
    'columns' => array (
        'hidden' => array (        
          'exclude' => 1,
          'label'   => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_accounts.hidden',
          'config'  => array (
            'type'    => 'check',
            'default' => '0'
          )
        ),
        'protocol' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_accounts.protocol',
            'config' => Array (
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            )
        ),
        'username' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_accounts.username',
            'config' => Array (
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            )
        ),
        'password' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_accounts.password',
            'config' => Array (
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            )
        ),
        'url' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_accounts.url',
            'config' => Array (
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            )
        ),
        'description' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_accounts.description',
            'config' => Array (
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            )
        ),
    ),
    'types' => array (
        '0' => array('showitem' => 'hidden;;;;1-1-1, protocol,username, password, url, description')
    ),
    'palettes' => array (
        '1' => array('showitem' => '')
    )
);


?>