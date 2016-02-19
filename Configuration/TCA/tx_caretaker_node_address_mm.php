<?php

$GLOBALS['TCA']['tx_caretaker_node_address_mm'] = array(
	'ctrl' => array(
		'hideTable' => 1,
		'label' => 'uid_address',
		'label_alt' => 'role',
		'label_alt_force' => 1,
		'iconfile' => 'EXT:caretaker/res/icons/nodeaddressrelation.png',
	),
	'interface' => array(
		'showRecordFieldList' => ''
	),
	'columns' => array(
		'uid_address' => array(
			'label' => 'LLL:EXT:tt_address/locallang_tca.xml:tt_address',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('tt_address') ? 'tt_address' : 'tx_caretaker_contactaddress',
				'wizards' => Array(
					'_PADDING' => 1,
					'_VERTICAL' => 1,
					'edit' => Array(
						'type' => 'script',
						'title' => 'Create new address',
						'icon' => 'add.gif',
						'params' => Array(
							'table' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('tt_address') ? 'tt_address' : 'tx_caretaker_contactaddress',
							'pid' => '0',
							'setValue' => 'prepend'
						),
						'module' => array(
							'name' => 'wizard_add'
						)
					)
				)
			)
		),
		'role' => array(
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_roles',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_caretaker_roles',
				'items' => array(
					Array('', 0),
				),
			)
		)
	),
	'types' => array(
		'0' => array('showitem' => 'uid_address;;1;;1-1-1, role')
	)
);
