<?php

$GLOBALS['TCA']['tx_caretaker_roles'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_roles',
		'label' => 'name',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY name',
		'delete' => 'deleted',
		'rootLevel' => -1,
		'enablecolumns' => array(
			'disabled' => 'hidden',
		),
		'iconfile' => 'EXT:caretaker/res/icons/role.png',
		'searchFields' => 'name, description'
	),
	'interface' => array(
		'showRecordFieldList' => 'hidden,id,name'
	),
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
