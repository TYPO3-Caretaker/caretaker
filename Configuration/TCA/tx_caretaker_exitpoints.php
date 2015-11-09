<?php

$extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
$advancedNotificationsEnabled = $extConfig['notifications.']['advanced.']['enabled'] == '1';

if ($advancedNotificationsEnabled) {
	$GLOBALS['TCA']['tx_caretaker_exitpoints'] = array(
		'ctrl' => array(
			'title' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints',
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
			'iconfile' => 'EXT:caretaker/res/icons/exitpoint.png',
			'requestUpdate' => 'service',
			'searchFields' => 'name, description'
		),
		'interface' => array(
			'showRecordFieldList' => 'hidden,id,name,description,service,config'
		),
		'columns' => Array(
			'hidden' => Array(
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
				'config' => Array(
					'type' => 'check',
					'default' => '0'
				),
			),
			'id' => array(
				'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints.id',
				'config' => array(
					'type' => 'input',
					'size' => 30,
					'eval' => 'required,nospace,unique'
				)
			),
			'name' => Array(
				'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints.name',
				'config' => Array(
					'type' => 'input',
					'size' => '255',
				)
			),
			'description' => Array(
				'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints.description',
				'config' => Array(
					'type' => 'text',
					'cols' => '50',
					'rows' => '5',
				)
			),
			'service' => array(
				'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints.service',
				'config' => Array(
					'type' => 'select',
					'renderType' => 'selectSingle',
					'items' => array_merge(
						array(
							0 => array(
								'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints.service.select_exitpoint',
								''
							)
						),
						\tx_caretaker_ServiceHelper::getTcaExitPointServiceItems()
					),
					'size' => 1,
					'maxitems' => 1,
				)
			),
			'config' => array(
				'displayCond' => 'FIELD:service:REQ:true',
				'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_exitpoints.config',
				'config' => [
					'type' => 'flex',
					'ds_pointerField' => 'service',
					'ds' => \tx_caretaker_ServiceHelper::getTcaExitPointConfigDs()
				]
			)
		),
		'types' => array(
			'0' => array('showitem' => 'id;;;;1-1-1, name, description, service, config')
		),
		'palettes' => array(
			'1' => array('showitem' => 'hidden')
		)
	);
}