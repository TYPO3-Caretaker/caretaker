<?php

return array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints',
		'label'     => 'name',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY name',
		'delete' => 'deleted',
		'enablecolumns' => array (
			'disabled' => 'hidden',
		),
		'iconfile'          => t3lib_extMgm::extRelPath('caretaker').'res/icons/exitpoint.png',
		'requestUpdate' => 'service',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => '',
	),
	'interface' => array (
		'showRecordFieldList' => 'hidden,id,name,description,service,config'
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
		'id' => array (
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints.id',
			'config' => array (
				'type' => 'input',
				'size' => 30,
				'eval' => 'required,nospace,unique'
			)
		),
		'name' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints.name',
			'config' => Array (
				'type' => 'input',
				'size' => '255',
			)
		),
		'description' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints.description',
			'config' => Array (
				'type' => 'text',
				'cols' => '50',
				'rows' => '5',
			)
		),
		'service' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints.service',
			'config' => Array (
				'type' => 'select',
				'items' => array (
					0 => array('LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints.service.select_exitpoint', '')
				),
				'size' => 1,
				'maxitems' => 1,
			)
		),
		'config' => array (
			'displayCond' => 'FIELD:service:REQ:true',
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints.config',
			'config' => Array (
				'type' => 'flex',
				'ds_pointerField' => 'service',
				'ds' => array()
			)
		)
	),
	'types' => array (
		'0' => array('showitem' => 'id;;;;1-1-1, name, description, service, config')
	),
	'palettes' => array (
		'1' => array('showitem' => 'hidden')
	)
);