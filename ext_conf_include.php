<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2009-2011 by n@work GmbH and networkteam GmbH
 *
 * All rights reserved
 *
 * This script is part of the Caretaker project. The Caretaker project
 * is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * This is a file of the caretaker project.
 * http://forge.typo3.org/projects/show/extension-caretaker
 *
 * Project sponsored by:
 * n@work GmbH - http://www.work.de
 * networkteam GmbH - http://www.networkteam.com/
 *
 * $Id$
 */
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// register Test-Services
tx_caretaker_ServiceHelper::registerCaretakerTestService($_EXTKEY, 'Classes/services/tests', 'tx_caretaker_ping', 'Ping', 'Retrieves System Informations');
tx_caretaker_ServiceHelper::registerCaretakerTestService($_EXTKEY, 'Classes/services/tests', 'tx_caretaker_http', 'HTTP', 'Call an URI and check the HTTP-Status');
tx_caretaker_ServiceHelper::registerCaretakerTestService($_EXTKEY, 'Classes/services/tests', 'tx_caretaker_Touch', 'Touch', 'Write a timestamp in a local file');

//register Notification-Services
tx_caretaker_ServiceHelper::registerCaretakerNotificationService($_EXTKEY, 'SimpleMailNotificationService', 'Classes/services/notifications/class.tx_caretaker_SimpleMailNotificationService.php', 'tx_caretaker_SimpleMailNotificationService');
tx_caretaker_ServiceHelper::registerCaretakerNotificationService($_EXTKEY, 'CliNotificationService', 'Classes/services/notifications/class.tx_caretaker_CliNotificationService.php', 'tx_caretaker_CliNotificationService');
tx_caretaker_ServiceHelper::registerCaretakerNotificationService($_EXTKEY, 'AdvancedNotificationService', 'Classes/services/notifications/advanced/class.tx_caretaker_AdvancedNotificationService.php', 'tx_caretaker_AdvancedNotificationService');

// register ExitPoint services
tx_caretaker_ServiceHelper::registerNotificationExitPoint($_EXTKEY, 'Classes/services/notifications/advanced/exitpoints', 'tx_caretaker_NotificationMail', 'E-Mail', 'Sends an e-mail');
tx_caretaker_ServiceHelper::registerNotificationExitPoint($_EXTKEY, 'Classes/services/notifications/advanced/exitpoints', 'tx_caretaker_NotificationFile', 'File', 'Writes to a file');
tx_caretaker_ServiceHelper::registerNotificationExitPoint($_EXTKEY, 'Classes/services/notifications/advanced/exitpoints', 'tx_caretaker_NotificationXmpp', 'XMPP/Jabber', 'Sends XMPP/Jabber messages');

// register ExtJS Panels
tx_caretaker_ServiceHelper::registerExtJsBackendPanel(
    'node-info',
    'caretaker-nodeinfo',
    array('EXT:caretaker/Resources/Public/Css/Overview.css'),
    array('EXT:caretaker/res/js/tx.caretaker.NodeInfo.js'),
    $_EXTKEY
);

tx_caretaker_ServiceHelper::registerExtJsBackendPanel(
    'node-charts',
    'caretaker-nodecharts',
    array('EXT:caretaker/Resources/Public/Css/Overview.css'),
    array('EXT:caretaker/res/js/tx.caretaker.NodeCharts.js'),
    $_EXTKEY
);

tx_caretaker_ServiceHelper::registerExtJsBackendPanel(
    'node-log',
    'caretaker-nodelog',
    array('EXT:caretaker/Resources/Public/Css/Overview.css'),
    array('EXT:caretaker/res/js/tx.caretaker.NodeLog.js'),
    $_EXTKEY
);

tx_caretaker_ServiceHelper::registerExtJsBackendPanel(
    'node-contacts',
    'caretaker-nodecontacts',
    array('EXT:caretaker/Resources/Public/Css/Overview.css'),
    array('EXT:caretaker/res/js/tx.caretaker.NodeContacts.js'),
    $_EXTKEY
);

tx_caretaker_ServiceHelper::registerExtJsBackendPanel(
    'node-problems',
    'caretaker-nodeproblems',
    array('EXT:caretaker/Resources/Public/Css/Overview.css'),
    array('EXT:caretaker/res/js/tx.caretaker.NodeProblems.js'),
    $_EXTKEY
);
