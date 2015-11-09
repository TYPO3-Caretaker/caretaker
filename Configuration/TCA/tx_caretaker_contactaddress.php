<?php

$GLOBALS['TCA']['tx_caretaker_contactaddress'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_contactaddress',
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
		'dividers2tabs' => 1,
		'iconfile' => 'EXT:caretaker/res/icons/contactaddress.png',
	),
	'interface' => array(
		'showRecordFieldList' => 'hidden,name,email,xmpp'
	),
	'columns' => Array(
		'hidden' => Array(
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array(
				'type' => 'check',
				'default' => '0'
			),
		),
		'name' => Array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_contactaddress.name',
			'config' => Array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'email' => Array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_contactaddress.email',
			'config' => Array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'xmpp' => Array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_contactaddress.xmpp',
			'config' => Array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'id;;;;1-1-1, name, email, xmpp')
	),
	'palettes' => array(
		'1' => array('showitem' => 'hidden')
	)
);
