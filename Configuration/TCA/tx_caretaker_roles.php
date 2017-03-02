<?php

$GLOBALS['TCA']['tx_caretaker_roles'] = array(
    'ctrl' => array(
        'title' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_roles',
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
        'iconfile' => 'EXT:caretaker/res/icons/role.png',
        'searchFields' => 'name, description',
    ),
    'interface' => array(
        'showRecordFieldList' => 'hidden,id,name',
    ),
    'columns' => array(
        'hidden' => array(
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
            'config' => array(
                'type' => 'check',
                'default' => '0',
            ),
        ),
        'id' => array(
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_roles.id',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'unique,trim',
            ),
        ),
        'name' => array(
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_roles.name',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'unique,trim',
            ),
        ),
        'description' => array(
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_roles.description',
            'config' => array(
                'type' => 'text',
                'cols' => '50',
                'rows' => '5',
            ),
        ),
    ),
    'types' => array(
        '0' => array('showitem' => 'id;;;;1-1-1, name, description'),
    ),
    'palettes' => array(
        '1' => array('showitem' => 'hidden'),
    ),
);
