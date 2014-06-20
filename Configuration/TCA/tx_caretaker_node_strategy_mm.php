<?php

return array (
	'ctrl' => array (
		'hideTable' => 1,

		'label' => 'uid_strategy',

		'iconfile'          => t3lib_extMgm::extRelPath('caretaker').'res/icons/nodeaddressrelation.png',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => '',
	),
	'interface' => array (
		'showRecordFieldList' => ''
	),
	'columns' => array (
		'uid_strategy' => array (
			'label' => 'LLL:EXT:tt_address/locallang_tca.xml:tx_caretaker_strategies',
			'config' => array (
				'type' => 'select',
				'foreign_table' => 'tx_caretaker_strategies'
			)
		)
	),
	'types' => array (
		'0' => array('showitem' => 'uid_strategy;;1;;1-1-1')
	)
);