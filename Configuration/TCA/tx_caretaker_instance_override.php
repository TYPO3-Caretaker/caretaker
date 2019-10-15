<?php

$GLOBALS['TCA']['tx_caretaker_instance_override'] = array(
    'ctrl' => array(
        'title' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance_override',
        'label' => 'type',
        'label_alt' => 'test,curl_option',
        'label_userFunc' => 'Caretaker\\Caretaker\\UserFunc\\LabelUserFunc->getLabel',
        'type' => 'type',
        'hideTable' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'rootLevel' => -1,
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'disabled',
        ),
    ),
    'interface' => array(
        'showitem' => '',
    ),
    'columns' => array(
        'type' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance_override.type',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => array(
                    array(
                        'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance_override.type.test_configuration',
                        'test_configuration',
                    ),
                    array(
                        'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance_override.type.curl_option',
                        'curl_option',
                    ),
                ),
                'default' => 'test_configuration',
            ),
        ),
        'test' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance_override.test',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_caretaker_test',
            ),
            'onChange' => 'reload',
        ),
        'test_hidden' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance_override.test_hidden',
            'config' => array(
                'type' => 'check',
            ),
        ),
        'test_configuration' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance_override.test_configuration',
            'config' => array(
                'type' => 'flex',
                'ds_pointerField' => 'test',
                'ds' => \tx_caretaker_ServiceHelper::getTcaTestConfigDsWithIds(),
            ),
            'displayCond' => 'FIELD:test:REQ:true',
        ),
        'curl_option' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance_override.curl_option',
            'config' => array(
                'type' => 'select',
                'size' => '1',
                'max' => '1',
                'items' => array(
                    array('', ''),
                    array('CURLOPT_SSL_VERIFYPEER', 'CURLOPT_SSL_VERIFYPEER'),
                    array('CURLOPT_TIMEOUT_MS', 'CURLOPT_TIMEOUT_MS'),
                    array('CURLOPT_INTERFACE', 'CURLOPT_INTERFACE'),
                    array('CURLOPT_USERPWD (user:password)', 'CURLOPT_USERPWD'),
                    array('CURLOPT_HTTPAUTH', 'CURLOPT_HTTPAUTH'),
                ),
                'renderType' => 'selectSingle',
            ),
            'onChange' => 'reload',
        ),
        'curl_value_int' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance_override.curl_value',
            'config' => array(
                'type' => 'input',
                'eval' => 'trim,int',
            ),
            'displayCond' => 'FIELD:curl_option:=:CURLOPT_TIMEOUT_MS',
        ),
        'curl_value_string' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance_override.curl_value',
            'config' => array(
                'type' => 'input',
                'eval' => 'trim',
            ),
            'displayCond' => 'FIELD:curl_option:IN:CURLOPT_INTERFACE,CURLOPT_USERPWD',
        ),
        'curl_value_bool' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance_override.curl_value',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => array(
                    array('LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance_override.curl_value.true', 'true'),
                    array('LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance_override.curl_value.false', 'false'),
                ),
            ),
            'displayCond' => 'FIELD:curl_option:=:CURLOPT_SSL_VERIFYPEER',
        ),
        'curl_value_httpauth' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance_override.curl_value',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => array(
                    array('CURLAUTH_ANY', CURLAUTH_ANY),
                    array('CURLAUTH_ANYSAFE', CURLAUTH_ANYSAFE),
                    array('CURLAUTH_BASIC', CURLAUTH_BASIC),
                    array('CURLAUTH_DIGEST', CURLAUTH_DIGEST),
                    array('CURLAUTH_GSSNEGOTIATE', CURLAUTH_GSSNEGOTIATE),
                    array('CURLAUTH_NTLM', CURLAUTH_NTLM),
                ),
            ),
            'displayCond' => 'FIELD:curl_option:=:CURLOPT_HTTPAUTH',
        ),
        'instance' => array(
            'label' => 'Instance',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_caretaker_instance',
            ),
        ),
    ),
    'types' => array(
        'test_configuration' => array(
            'showitem' => 'type,test,test_hidden,test_configuration,--palette--;;instance',
        ),
        'curl_option' => array(
            'showitem' => 'type,curl_option,curl_value_int,curl_value_string,curl_value_bool,curl_value_httpauth,--palette--;;instance',
        ),
    ),
    'palettes' => array(
        'instance' => array(
            'showitem' => 'instance',
            'isHiddenPalette' => true,
        ),
    ),
);
