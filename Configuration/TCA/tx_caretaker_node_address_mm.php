<?php

return array (
	'ctrl' => array (
		'hideTable' => 1,

		'label' => 'uid_address',
		'label_alt' => 'role',
		'label_alt_force' => 1,

		'iconfile'          => t3lib_extMgm::extRelPath('caretaker').'res/icons/nodeaddressrelation.png',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => '',
	),
	'interface' => array (
		'showRecordFieldList' => ''
	),
	'columns' => array (
		'uid_address' => array (
			'label' => 'LLL:EXT:tt_address/locallang_tca.xml:tt_address',
			'config' => array (
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
							'table'=>'tt_address',
							'pid' => '0',
							'setValue' => 'prepend'
						),
						'script' => 'wizard_add.php'
					)
				)
			)
		),
		'role' => array (
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_roles',
			'config' => array (
				'type' => 'select',
				'foreign_table' => 'tx_caretaker_roles',
				'items' => array(
					Array('',          0),
				),
			)
		)
	),
	'types' => array (
		'0' => array('showitem' => 'uid_address;;1;;1-1-1, role')
	)
);