<?php

use TYPO3\CMS\Core\Utility\VersionNumberUtility;

$extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
$advancedNotificationsEnabled = $extConfig['notifications.']['advanced.']['enabled'] == '1';
$enableNewConfigurationOverrides = $extConfig['features.']['newConfigurationOverrides.']['enabled'] == '1';
if (VersionNumberUtility::convertVersionNumberToInteger(VersionNumberUtility::getCurrentTypo3Version()) >= VersionNumberUtility::convertVersionNumberToInteger('7.5.0')) {
    // enable new configurations overrides automatically with 7.5 and later
    $enableNewConfigurationOverrides = true;
}

$GLOBALS['TCA']['tx_caretaker_instance'] = [
    'ctrl' => [
        'title' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => 'ORDER BY title',
        'delete' => 'deleted',
        'rootLevel' => -1,
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group',
        ],
        'dividers2tabs' => 1,
        'iconfile' => 'EXT:caretaker/res/icons/instance.png',
        'searchFields' => 'title, description, url, host',
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden,instancegroup,testgroups,tests,request_mode,encryption_mode,encryption_key,project_namename,project_manager,domain,additional_domains,contacts,server_type,server_provider,server_customer_id,server_other,cms_url,cms_admin,cms_pwd,cms_install_pwd,accesses,other,request_url,contacts',
    ],
    'columns' => [
        'hidden' => [
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
        'starttime' => [
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.starttime',
            'config' => [
                'type' => 'input',
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'default' => '0',
                'checkbox' => '0',
            ],
        ],
        'endtime' => [
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.endtime',
            'config' => [
                'type' => 'input',
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0',
                'range' => [
                    'upper' => mktime(0, 0, 0, 12, 31, 2020),
                    'lower' => mktime(0, 0, 0, date('m') - 1, date('d'), date('Y')),
                ],
            ],
        ],
        'fe_group' => [
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.fe_group',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'items' => [
                    ['LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1],
                    ['LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2],
                    ['LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--'],
                ],
                'exclusiveKeys' => '-1,-2',
                'foreign_table' => 'fe_groups',
                'foreign_table_where' => 'ORDER BY fe_groups.title',
                'maxitems' => 20,
                'size' => 7,
            ],
        ],
        'title' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.title',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ],
        ],
        'description' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.description',
            'config' => [
                'type' => 'text',
                'cols' => '50',
                'rows' => '5',
            ],
            'defaultExtras' => 'richtext',
        ],
        'url' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.url',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ],
        ],
        'host' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.host',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ],
        ],
        'groups' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.groups',
            'config' => [
                'type' => 'select',
                'renderMode' => 'tree',
                'renderType' => 'selectTree',
                'treeConfig' => [
                    'parentField' => 'parent_group',
                    'appearance' => [
                        'showHeader' => true,
                    ],
                ],
                'foreign_table' => 'tx_caretaker_testgroup',
                'foreign_table_where' => 'ORDER BY tx_caretaker_testgroup.sorting ASC',
                'minitems' => 0,
                'maxitems' => 50,
                'MM' => 'tx_caretaker_instance_testgroup_mm',
            ],
        ],
        'tests' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.tests',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_caretaker_test',
                'foreign_table_where' => 'ORDER BY tx_caretaker_test.title ASC',
                'MM' => 'tx_caretaker_instance_test_mm',
                'MM_opposite_field' => 'instances',
                'size' => 5,
                'autoSizeMax' => 10,
                'minitems' => 0,
                'maxitems' => 10000,
                'wizards' => [
                    '_PADDING' => 1,
                    '_VERTICAL' => 1,
                    'edit' => [
                        'type' => 'popup',
                        'title' => 'Edit Test',
                        'module' => [
                            'name' => 'wizard_edit',
                        ],
                        'icon' => 'edit2.gif',
                        'popup_onlyOpenIfSelected' => 1,
                        'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
                    ],
                    'add' => [
                        'type' => 'script',
                        'title' => 'Create new Test',
                        'icon' => 'add.gif',
                        'params' => [
                            'table' => 'tx_caretaker_test',
                            'pid' => '###CURRENT_PID###',
                            'setValue' => 'prepend',
                        ],
                        'module' => [
                            'name' => 'wizard_add',
                        ],
                    ],
                ],
            ],
        ],
        'public_key' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.public_key',
            'displayCond' => 'EXT:caretaker_instance:LOADED:true',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'max' => '1024',
            ],
        ],
        'instancegroup' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.instancegroup',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_caretaker_instancegroup',
                'foreign_table_where' => 'ORDER BY tx_caretaker_instancegroup.title ASC',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'items' => [
                    ['', '0'],
                ],

            ],
        ],
        'testconfigurations' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_conf',
            'config' => [
                'type' => 'flex',
                'ds' => [
                    'default' => 'FILE:EXT:caretaker/res/flexform/ds.tx_caretaker_instance_testconfiguration.xml',
                ],
            ],
        ],
        'configuration_overrides' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_test.test_conf',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_caretaker_instance_override',
                'foreign_field' => 'instance',
                'appearance' => [
                    'newRecordLinkAddTitle' => true,
                    'levelLinksPosition' => 'both',
                    'useSortable' => true,
                    'enabledControls' => [
                        'info' => false,
                        'new' => true,
                        'dragdrop' => true,
                        'sort' => false,
                        'hide' => true,
                        'delete' => true,
                        'localize' => false,
                    ],
                ],
                'behavior' => [
                    'enableCascadingDelete' => true,
                ],
            ],
        ],
        'contacts' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.contacts',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_caretaker_node_address_mm',
                'foreign_field' => 'uid_node',
                'foreign_table_field' => 'node_table',
                // 'foreign_selector' => 'uid_address',
                'appearance' => [
                    'collapseAll' => true,
                    'expandSingle' => true,
                ],
            ],
        ],
        'notification_strategies' => [
            'label' => 'LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_general.notification_strategies',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_caretaker_node_strategy_mm',
                'foreign_field' => 'uid_node',
                'foreign_table_field' => 'node_table',
                'foreign_selector' => 'uid_strategy',
                'appearance' => [
                    'collapseAll' => true,
                    'expandSingle' => true,
                ],
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'title;;;;2-2-2, instancegroup, url;;;;3-3-3, host, public_key,' .
                '--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.tab.description, description, ' .
                '--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.tab.relations, groups, tests, ' .
                '--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.tab.contacts, contacts, ' .
                ($advancedNotificationsEnabled ? '--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.tab.notifications, notification_strategies, ' : '') .
                '--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.tab.testconfigurations, ' . ($enableNewConfigurationOverrides ? 'configuration_overrides, ' : 'testconfigurations,') .
                '--div--;LLL:EXT:caretaker/locallang_db.xml:tx_caretaker_instance.tab.access, hidden, starttime, endtime, fe_group',
        ],
    ],
    'palettes' => [],
];
