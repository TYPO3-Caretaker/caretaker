<?php

$GLOBALS['TCA']['tx_caretaker_instance_override'] = [
    'ctrl' => [
        'title' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance_override',
        'label' => 'type',
        'label_alt' => 'test,curl_option',
        'label_userFunc' => 'Caretaker\\Caretaker\\UserFunc\\LabelUserFunc->getLabel',
        'type' => 'type',
        'hideTable' => true,
        'requestUpdate' => 'test,curl_option',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'rootLevel' => -1,
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'disabled',
        ],
    ],
    'interface' => [
        'showitem' => '',
    ],
    'columns' => [
        'type' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance_override.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance_override.type.test_configuration',
                        'test_configuration',
                    ],
                    [
                        'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance_override.type.curl_option',
                        'curl_option',
                    ],
                ],
                'default' => 'test_configuration',
            ],
        ],
        'test' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance_override.test',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_caretaker_test',
            ],
        ],
        'test_hidden' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance_override.test_hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'test_configuration' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance_override.test_configuration',
            'config' => [
                'type' => 'flex',
                'ds_pointerField' => 'test',
                'ds' => \tx_caretaker_ServiceHelper::getTcaTestConfigDsWithIds(),
            ],
            'displayCond' => 'FIELD:test:REQ:true',
        ],
        'curl_option' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance_override.curl_option',
            'config' => [
                'type' => 'select',
                'size' => '1',
                'max' => '1',
                'items' => [
                    ['', ''],
                    ['CURLOPT_SSL_VERIFYPEER', 'CURLOPT_SSL_VERIFYPEER'],
                    ['CURLOPT_TIMEOUT_MS', 'CURLOPT_TIMEOUT_MS'],
                    ['CURLOPT_INTERFACE', 'CURLOPT_INTERFACE'],
                    ['CURLOPT_USERPWD (user:password)', 'CURLOPT_USERPWD'],
                    ['CURLOPT_HTTPAUTH', 'CURLOPT_HTTPAUTH'],
                ],
            ],
        ],
        'curl_value_int' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance_override.curl_value',
            'config' => [
                'type' => 'input',
                'eval' => 'trim,int',
            ],
            'displayCond' => 'FIELD:curl_option:=:CURLOPT_TIMEOUT_MS',
        ],
        'curl_value_string' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance_override.curl_value',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
            ],
            'displayCond' => 'FIELD:curl_option:IN:CURLOPT_INTERFACE,CURLOPT_USERPWD',
        ],
        'curl_value_bool' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance_override.curl_value',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance_override.curl_value.true', 'true'],
                    ['LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance_override.curl_value.false', 'false'],
                ],
            ],
            'displayCond' => 'FIELD:curl_option:=:CURLOPT_SSL_VERIFYPEER',
        ],
        'curl_value_httpauth' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance_override.curl_value',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['CURLAUTH_ANY', CURLAUTH_ANY],
                    ['CURLAUTH_ANYSAFE', CURLAUTH_ANYSAFE],
                    ['CURLAUTH_BASIC', CURLAUTH_BASIC],
                    ['CURLAUTH_DIGEST', CURLAUTH_DIGEST],
                    ['CURLAUTH_GSSNEGOTIATE', CURLAUTH_GSSNEGOTIATE],
                    ['CURLAUTH_NTLM', CURLAUTH_NTLM],
                ],
            ],
            'displayCond' => 'FIELD:curl_option:=:CURLOPT_HTTPAUTH',
        ],
        'instance' => [
            'label' => 'Instance',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_caretaker_instance',
            ],
        ],
    ],
    'types' => [
        'test_configuration' => [
            'showitem' => 'type,test,test_hidden,test_configuration,--palette--;;instance',
        ],
        'curl_option' => [
            'showitem' => 'type,curl_option,curl_value_int,curl_value_string,curl_value_bool,curl_value_httpauth,--palette--;;instance',
        ],
    ],
    'palettes' => [
        'instance' => [
            'showitem' => 'instance',
            'isHiddenPalette' => true,
        ],
    ],
];
