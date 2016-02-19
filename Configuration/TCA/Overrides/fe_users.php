<?php

// add API-Key to fe_user record
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
	'fe_users',
	array(
		'tx_caretaker_api_key' => Array(
			'exclude' => 1,
			'label' => 'LLL:EXT:caretaker/locallang_db.xml:fe_users.tx_caretaker_api_key',
			'config' => Array(
				'type' => 'input'
			)
		)
	)
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('fe_users', 'tx_caretaker_api_key');
