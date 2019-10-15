<?php

$extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
$advancedNotificationsEnabled = $extConfig['notifications.']['advanced.']['enabled'] == '1';

if ($advancedNotificationsEnabled) {
    $GLOBALS['TCA']['tx_caretaker_node_strategy_mm'] = array(
        'ctrl' => array(
            'hideTable' => 1,
            'label' => 'uid_strategy',
            'iconfile' => 'EXT:caretaker/res/icons/nodeaddressrelation.png',
        ),
        'interface' => array(
            'showRecordFieldList' => '',
        ),
        'columns' => array(
            'uid_strategy' => array(
                'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_strategies',
                'config' => array(
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'foreign_table' => 'tx_caretaker_strategies',
                ),
            ),
        ),
        'types' => array(
            '0' => array('showitem' => 'uid_strategy, --palette--;;1'),
        ),
    );
}
