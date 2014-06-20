<?php

return array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_roles',
		'label'     => 'name',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY name',
		'delete' => 'deleted',
		'enablecolumns' => array (
			'disabled' => 'hidden',
		),
		'iconfile'          => t3lib_extMgm::extRelPath('caretaker').'res/icons/role.png',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => '',
	),
	'interface' => array (
		'showRecordFieldList' => 'hidden,id,name'
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
		'id' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_roles.id',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'unique,trim',
			)
		),
		'name' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_roles.name',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'unique,trim',
			)
		),
		'description' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_roles.description',
			'config' => Array (
				'type' => 'text',
				'cols' => '50',
				'rows' => '5',
			)
		)
	),
	'types' => array (
		'0' => array('showitem' => 'id;;;;1-1-1, name, description')
	),
	'palettes' => array (
		'1' => array('showitem' => 'hidden')
	)
);