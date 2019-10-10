<?php

use TYPO3\CMS\Core\Utility\VersionNumberUtility;

$extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
$advancedNotificationsEnabled = $extConfig['notifications.']['advanced.']['enabled'] == '1';
$enableNewConfigurationOverrides = $extConfig['features.']['newConfigurationOverrides.']['enabled'] == '1';
if (VersionNumberUtility::convertVersionNumberToInteger(VersionNumberUtility::getCurrentTypo3Version()) >= VersionNumberUtility::convertVersionNumberToInteger('7.5.0')) {
    // enable new configurations overrides automatically with 7.5 and later
    $enableNewConfigurationOverrides = true;
}

$GLOBALS['TCA']['tx_caretaker_instance'] = array(
    'ctrl' => array(
        'title' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => 'ORDER BY title',
        'delete' => 'deleted',
        'rootLevel' => -1,
        'enablecolumns' => array(
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group',
        ),
        'dividers2tabs' => 1,
        'iconfile' => 'EXT:caretaker/res/icons/instance.png',
        'searchFields' => 'title, description, url, host',
    ),
    'interface' => array(
        'showRecordFieldList' => 'hidden,instancegroup,testgroups,tests,request_mode,encryption_mode,encryption_key,project_namename,project_manager,domain,additional_domains,contacts,server_type,server_provider,server_customer_id,server_other,cms_url,cms_admin,cms_pwd,cms_install_pwd,accesses,other,request_url,contacts',
    ),
    'columns' => array(
        'hidden' => array(
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => array(
                'type' => 'check',
                'default' => '0',
            ),
        ),
        'starttime' => array(
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => array(
                'type' => 'input',
                'size' => '8',
                'eval' => 'date',
                'default' => '0',
                'checkbox' => '0',
                'renderType' => 'inputDateTime',
            ),
        ),
        'endtime' => array(
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => array(
                'type' => 'input',
                'size' => '8',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0',
                'range' => array(
                    'upper' => mktime(0, 0, 0, 12, 31, 2020),
                    'lower' => mktime(0, 0, 0, date('m') - 1, date('d'), date('Y')),
                ),
                'renderType' => 'inputDateTime',
            ),
        ),
        'fe_group' => array(
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.fe_group',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'items' => array(
                    array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--'),
                ),
                'exclusiveKeys' => '-1,-2',
                'foreign_table' => 'fe_groups',
                'foreign_table_where' => 'ORDER BY fe_groups.title',
                'maxitems' => 20,
                'size' => 7,
            ),
        ),
        'title' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance.title',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ),
        ),
        'description' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance.description',
            'config' => array(
                'type' => 'text',
                'cols' => '50',
                'rows' => '5',
                'enableRichtext' => true,
            ),
        ),
        'url' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance.url',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ),
        ),
        'host' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance.host',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ),
        ),
        'groups' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance.groups',
            'config' => array(
                'type' => 'select',
                'renderMode' => 'tree',
                'renderType' => 'selectTree',
                'treeConfig' => array(
                    'parentField' => 'parent_group',
                    'appearance' => array(
                        'showHeader' => true,
                    ),
                ),
                'foreign_table' => 'tx_caretaker_testgroup',
                'foreign_table_where' => 'ORDER BY tx_caretaker_testgroup.sorting ASC',
                'minitems' => 0,
                'maxitems' => 50,
                'MM' => 'tx_caretaker_instance_testgroup_mm',
            ),
        ),
        'tests' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance.tests',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_caretaker_test',
                'foreign_table_where' => ' AND deleted = 0 ORDER BY tx_caretaker_test.title ASC',
                'MM' => 'tx_caretaker_instance_test_mm',
                'MM_opposite_field' => 'instances',
                'size' => 5,
                'autoSizeMax' => 10,
                'minitems' => 0,
                'maxitems' => 10000,
                'fieldControl' => array(
                    'addRecord' => array(
                        'pid' => '###CURRENT_PID###',
                        'table' => 'tx_caretaker_test',
                        'title' => 'Create new Test',
                        'setValue' => 'prepend',
                    ),
                    'editPopup' => array(
                        'title' => 'Edit Test',
                        'windowOpenParameters' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
                    ),
                ),
            ),
        ),
        'public_key' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance.public_key',
            'config' => array(
                'type' => 'input',
                'eval' => 'trim',
                'max' => '1024',
            ),
        ),
        'instancegroup' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance.instancegroup',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_caretaker_instancegroup',
                'foreign_table_where' => 'ORDER BY tx_caretaker_instancegroup.title ASC',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'items' => array(
                    array('', '0'),
                ),
            ),
        ),
        'testconfigurations' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_test.test_conf',
            'config' => array(
                'type' => 'flex',
                'ds' => array(
                    'default' => 'FILE:EXT:caretaker/res/flexform/ds.tx_caretaker_instance_testconfiguration.xml',
                ),
            ),
        ),
        'configuration_overrides' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_test.test_conf',
            'config' => array(
                'type' => 'inline',
                'foreign_table' => 'tx_caretaker_instance_override',
                'foreign_field' => 'instance',
                'appearance' => array(
                    'newRecordLinkAddTitle' => true,
                    'levelLinksPosition' => 'both',
                    'useSortable' => true,
                    'enabledControls' => array(
                        'info' => false,
                        'new' => true,
                        'dragdrop' => true,
                        'sort' => false,
                        'hide' => true,
                        'delete' => true,
                        'localize' => false,
                    ),
                ),
                'behavior' => array(
                    'enableCascadingDelete' => true,
                ),
            ),
        ),
        'contacts' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance.contacts',
            'config' => array(
                'type' => 'inline',
                'foreign_table' => 'tx_caretaker_node_address_mm',
                'foreign_field' => 'uid_node',
                'foreign_table_field' => 'node_table',
                // 'foreign_selector' => 'uid_address',
                'appearance' => array(
                    'collapseAll' => true,
                    'expandSingle' => true,
                ),
            ),
        ),
        'notification_strategies' => array(
            'label' => 'LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_general.notification_strategies',
            'config' => array(
                'type' => 'inline',
                'foreign_table' => 'tx_caretaker_node_strategy_mm',
                'foreign_field' => 'uid_node',
                'foreign_table_field' => 'node_table',
                'foreign_selector' => 'uid_strategy',
                'appearance' => array(
                    'collapseAll' => true,
                    'expandSingle' => true,
                ),
            ),
        ),
    ),
    'types' => array(
        '0' => array(
            'showitem' => '
                title, 
                instancegroup, 
                url, 
                host, 
                public_key, 
                --div--;LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance.tab.description, description, 
                --div--;LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance.tab.relations, groups, tests, 
                --div--;LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance.tab.contacts, contacts, ' .
                ($advancedNotificationsEnabled ? '--div--;LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance.tab.notifications, notification_strategies, ' : '') .
                '--div--;LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance.tab.testconfigurations, ' .
                ($enableNewConfigurationOverrides ? 'configuration_overrides, ' : 'testconfigurations,') .
                '--div--;LLL:EXT:caretaker/Resources/Private/Language/locallang_db.xlf:tx_caretaker_instance.tab.access, hidden, starttime, endtime, fe_group',
        ),
    ),
    'palettes' => array(),
);
