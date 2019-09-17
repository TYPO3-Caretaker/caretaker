<?php

$GLOBALS['TCA']['tx_caretaker_contactaddress'] = array(
    'ctrl' => array(
        'title' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_contactaddress',
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
        'dividers2tabs' => 1,
        'iconfile' => 'EXT:caretaker/res/icons/contactaddress.png',
    ),
    'interface' => array(
        'showRecordFieldList' => 'hidden,name,email,xmpp',
    ),
    'columns' => array(
        'hidden' => array(
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => array(
                'type' => 'check',
                'default' => '0',
            ),
        ),
        'name' => array(
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_contactaddress.name',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ),
        ),
        'email' => array(
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_contactaddress.email',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ),
        ),
        'xmpp' => array(
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_contactaddress.xmpp',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ),
        ),
    ),
    'types' => array(
        '0' => array('showitem' => 'id, name, email, xmpp'),
    ),
    'palettes' => array(
        '1' => array('showitem' => 'hidden'),
    ),
);
