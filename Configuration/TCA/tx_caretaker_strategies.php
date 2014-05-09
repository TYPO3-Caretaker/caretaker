<?php

return array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_strategies',
		'label'     => 'name',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY name',
		'delete' => 'deleted',
		'enablecolumns' => array (
			'disabled' => 'hidden',
		),
		'iconfile'          => t3lib_extMgm::extRelPath('caretaker').'res/icons/strategy.png',
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
			'label'   => 'LLL:EXT:lang/locallang_general.php:LGL.disable',
			'config'  => Array (
				'type'    => 'check',
				'default' => '0'
			),
		),
		'name' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_strategies.name',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'unique,trim',
			)
		),
		'description' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_strategies.description',
			'config' => Array (
				'type' => 'text',
				'cols' => '50',
				'rows' => '5',
			)
		),
		'config' => array (
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_strategies.config',
			'config' => Array (
				'type' => 'text',
				'cols' => 50,
				'rows' => 50
			)
		)
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;;;1-1-1, id;;;;1-1-1, name, description, config')
	),
	'palettes' => array (
		'1' => array()
	)
);