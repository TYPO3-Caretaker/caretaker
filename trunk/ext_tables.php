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
 * $$Id: ext_tables.php 46 2008-06-19 16:09:17Z martin $$
 */

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_caretaker_test'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test',
		'label'     => 'title',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',
		'delete' => 'deleted',
		'dividers2tabs'=> 1,
	    'enablecolumns' => array (        
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
    	),
		'type' => 'testservice',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/test.png',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => 'hidden,exec_interval, testservice, testconf, name, last_exec',
	),
);

$TCA['tx_caretaker_instance'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance',
		'label'     => 'title',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',
		'delete' => 'deleted',
		'enablecolumns' => array (        
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'dividers2tabs'=> 1,
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/instance.png',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => 'hidden,instancegroup,testgroups, tests, request_mode, encryption_mode, encryption_key, project_name, project_manager, domain, additional_domains, contacts, server_type, server_provider, server_customer_id, server_more, cms_url, cms_admin, cms_pwd, cms_install_pwd, accounts, contract, request_url',
	)
);

$TCA['tx_caretaker_instance_test_rel'] = array (
	'ctrl' => array (
		'title'     => 'Instance >> Test Relation',
		'label'     => 'instance_id',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'delete' => 'deleted',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/test.png',
		'enablecolumns' => array (        
			'disabled' => 'hidden',
		),
	),
	'feInterface' => array ('fe_admin_fieldList' => '' )
);

$TCA['tx_caretaker_group'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_group',
		'label'     => 'title',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',
		'delete' => 'deleted',
		'treeParentField' => 'parent_group',
		'enablecolumns' => array (        
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'dividers2tabs'=> 1,
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/group.png',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => 'hidden, main_instance,name,testgroups,tests,contacts,accounts',
	)
);

$TCA['tx_caretaker_group_test_rel'] = array (
	'ctrl' => array (
		'title'     => 'Group >> Test Relation',
		'label'     => 'group_id',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'delete' => 'deleted',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/test.png',
		'enablecolumns' => array (        
			'disabled' => 'hidden',
		),
	),
	'feInterface' => array ('fe_admin_fieldList' => '' )
);


/*
$TCA['tx_caretaker_testresults'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_testresults',
		'label'     => 'uid',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',
		'delete' => 'deleted',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/test_result.png',
	),
	'feInterface' => array ('fe_admin_fieldList' => '' )
);
*/

$TCA['tx_caretaker_accounts'] = array (
    'ctrl' => array (
        'title'     => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_accounts',
        'label'     => 'protocol',
        'tstamp'    => 'tstamp',
        'crdate'    => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => 'ORDER BY crdate',
        'delete' => 'deleted',
        'enablecolumns' => array (        
          'disabled' => 'hidden',
        ),
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
        'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/account.png',
    ),
    'feInterface' => array ( 'fe_admin_fieldList' => 'hidden, protocol, username, password, url, description' )
);

	// add plugin
/*
t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages,recursive';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/pi1/flexform_ds.xml');

t3lib_extMgm::addPlugin(array('LLL:EXT:'.$_EXTKEY.'/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');

t3lib_extMgm::addStaticFile($_EXTKEY,'res/ts/','Caretaker');


if (TYPO3_MODE == 'BE')	{
	t3lib_extMgm::addModule('web','txcaretakerM1','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');
}
*/
	// include the service definitions
//include_once(t3lib_extMgm::extPath('caretaker').'ext_caretaker_services.php');

if (TYPO3_MODE=='BE') {
	include_once(t3lib_extMgm::extPath($_EXTKEY).'classes/class.tx_caretaker_befunctions.php');
}

?>