<?php

$extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
$advancedNotificationsEnabled = $extConfig['notifications.']['advanced.']['enabled'] == '1';

if ($advancedNotificationsEnabled) {
    $GLOBALS['TCA']['tx_caretaker_strategies'] = array(
        'ctrl' => array(
            'title' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_strategies',
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
            'iconfile' => 'EXT:caretaker/res/icons/strategy.png',
            'searchFields' => 'name, description',
        ),
        'interface' => array(
            'showRecordFieldList' => 'hidden,id,name',
        ),
        'columns' => array(
            'hidden' => array(
                'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.disable',
                'config' => array(
                    'type' => 'check',
                    'default' => '0',
                ),
            ),
            'name' => array(
                'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_strategies.name',
                'config' => array(
                    'type' => 'input',
                    'size' => '30',
                    'eval' => 'unique,trim',
                ),
            ),
            'description' => array(
                'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_strategies.description',
                'config' => array(
                    'type' => 'text',
                    'cols' => '50',
                    'rows' => '5',
                ),
            ),
            'config' => array(
                'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_strategies.config',
                'config' => array(
                    'type' => 'text',
                    'cols' => 50,
                    'rows' => 50,
                ),
            ),
        ),
        'types' => array(
            '0' => array('showitem' => 'hidden, id, name, description, config'),
        ),
        'palettes' => array(
            '1' => array(),
        ),
    );
}
