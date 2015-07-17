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

/**
 * Register necessary class names with autoloader
 */

$caretakerExtPath = t3lib_extMgm::extPath('caretaker');

return array(

	/* helpers */
		'tx_caretaker_constants' => $caretakerExtPath . 'classes/class.tx_caretaker_Constants.php',
		'tx_caretaker_servicehelper' => $caretakerExtPath . 'classes/helpers/class.tx_caretaker_ServiceHelper.php',
		'tx_caretaker_locallizationhelper' => $caretakerExtPath . 'classes/helpers/class.tx_caretaker_LocallizationHelper.php',
		'tx_caretaker_extensionmanagerhelper' => $caretakerExtPath . 'classes/helpers/class.tx_caretaker_ExtensionManagerHelper.php',
		'tx_caretaker_latestversionshelper' => $caretakerExtPath . 'classes/helpers/class.tx_caretaker_LatestVersionsHelper.php',

	/* plugins */
		'tx_caretaker_pibase' => $caretakerExtPath . '/pi_base/class.tx_caretaker_pibase.php',

	/* notification services */
		'tx_caretaker_abstractnotificationservice' => $caretakerExtPath . 'classes/services/notifications/class.tx_caretaker_AbstractNotificationService.php',
		'tx_caretaker_simplemailnotificationservice' => $caretakerExtPath . 'classes/services/notifications/class.tx_caretaker_SimpleMailNotificationService.php',
		'tx_caretaker_clinotificationservice' => $caretakerExtPath . 'classes/services/notifications/class.tx_caretaker_CliNotificationService.php',
		'tx_caretaker_advancednotificationservice' => $caretakerExtPath . 'classes/services/notifications/advanced/class.tx_caretaker_AdvancedNotificationService.php',

	/* notification exit points */
		'tx_caretaker_notificationbaseexitpoint' => $caretakerExtPath . 'classes/services/notifications/advanced/exitpoints/class.tx_caretaker_NotificationBaseExitPoint.php',
		'tx_caretaker_notificationmailexitpoint' => $caretakerExtPath . 'classes/services/notifications/advanced/exitpoints/class.tx_caretaker_NotificationMailExitPoint.php',
		'tx_caretaker_notificationfileexitpoint' => $caretakerExtPath . 'classes/services/notifications/advanced/exitpoints/class.tx_caretaker_NotificationFileExitPoint.php',

	/* interfaces */
		'tx_caretaker_resultrangerenderer' => $caretakerExtPath . 'interfaces/interface.tx_caretaker_ResultRangeRenderer.php',
		'tx_caretaker_testserviceinterface' => $caretakerExtPath . 'interfaces/interface.tx_caretaker_TestServiceInterface.php',
		'tx_caretaker_notificationserviceinterface' => $caretakerExtPath . 'interfaces/interface.tx_caretaker_NotificationServiceInterface.php',
		'tx_caretaker_notificationexitpointinterface' => $caretakerExtPath . 'interfaces/interface.tx_caretaker_NotificationExitPointInterface.php',

	/* testrunner */
		'tx_caretaker_testrunnertask' => $caretakerExtPath . 'scheduler/class.tx_caretaker_testrunnertask.php',
		'tx_caretaker_testrunnertask_additionalfieldprovider' => $caretakerExtPath . 'scheduler/class.tx_caretaker_testrunnertask_additionalfieldprovider.php',
		'tx_caretaker_terupdatetask' => $caretakerExtPath . 'scheduler/class.tx_caretaker_terupdatetask.php',
		'tx_caretaker_terupdatetask_additionalfieldprovider' => $caretakerExtPath . 'scheduler/class.tx_caretaker_terupdatetask_additionalfieldprovider.php',
		'tx_caretaker_typo3versionnumbersupdatetask' => $caretakerExtPath . 'scheduler/class.tx_caretaker_typo3versionnumbersupdatetask.php',

	/* nodes */
		'tx_caretaker_abstractnode' => $caretakerExtPath . 'classes/nodes/class.tx_caretaker_AbstractNode.php',
		'tx_caretaker_aggregatornode' => $caretakerExtPath . 'classes/nodes/class.tx_caretaker_AggregatorNode.php',
		'tx_caretaker_instancenode' => $caretakerExtPath . 'classes/nodes/class.tx_caretaker_InstanceNode.php',
		'tx_caretaker_instancegroupnode' => $caretakerExtPath . 'classes/nodes/class.tx_caretaker_InstancegroupNode.php',
		'tx_caretaker_rootnode' => $caretakerExtPath . 'classes/nodes/class.tx_caretaker_RootNode.php',
		'tx_caretaker_testnode' => $caretakerExtPath . 'classes/nodes/class.tx_caretaker_TestNode.php',
		'tx_caretaker_testgroupnode' => $caretakerExtPath . 'classes/nodes/class.tx_caretaker_TestgroupNode.php',

	/* repositories */
		'tx_caretaker_aggregatorresultrepository' => $caretakerExtPath . 'classes/repositories/class.tx_caretaker_AggregatorResultRepository.php',
		'tx_caretaker_testresultrepository' => $caretakerExtPath . 'classes/repositories/class.tx_caretaker_TestResultRepository.php',
		'tx_caretaker_noderepository' => $caretakerExtPath . 'classes/repositories/class.tx_caretaker_NodeRepository.php',
		'tx_caretaker_contactrepository' => $caretakerExtPath . 'classes/repositories/class.tx_caretaker_ContactRepository.php',

	/* results */
		'tx_caretaker_noderesult' => $caretakerExtPath . 'classes/results/class.tx_caretaker_NodeResult.php',
		'tx_caretaker_aggregatorresult' => $caretakerExtPath . 'classes/results/class.tx_caretaker_AggregatorResult.php',
		'tx_caretaker_testresult' => $caretakerExtPath . 'classes/results/class.tx_caretaker_TestResult.php',
		'tx_caretaker_noderesultrange' => $caretakerExtPath . 'classes/results/class.tx_caretaker_NodeResultRange.php',
		'tx_caretaker_aggregatorresultrange' => $caretakerExtPath . 'classes/results/class.tx_caretaker_AggregatorResultRange.php',
		'tx_caretaker_testresultrange' => $caretakerExtPath . 'classes/results/class.tx_caretaker_TestResultRange.php',
		'tx_caretaker_resultmessage' => $caretakerExtPath . 'classes/results/class.tx_caretaker_ResultMessage.php',

	/* contacts */
		'tx_caretaker_contact' => $caretakerExtPath . 'classes/contacts/class.tx_caretaker_Contact.php',
		'tx_caretaker_contactrole' => $caretakerExtPath . 'classes/contacts/class.tx_caretaker_ContactRole.php',

	/* renderers */
		'tx_caretaker_chartrendererbase' => $caretakerExtPath . 'classes/renderer/chart/class.tx_caretaker_ChartRendererBase.php',
		'tx_caretaker_testresultrangechartrenderer' => $caretakerExtPath . 'classes/renderer/chart/class.tx_caretaker_TestResultRangeChartRenderer.php',
		'tx_caretaker_multipletestresultrangechartrenderer' => $caretakerExtPath . 'classes/renderer/chart/class.tx_caretaker_MultipleTestResultRangeChartRenderer.php',
		'tx_caretaker_aggregatorresultrangechartrenderer' => $caretakerExtPath . 'classes/renderer/chart/class.tx_caretaker_AggregatorResultRangeChartRenderer.php',

	/* services */
		'tx_caretaker_testservicebase' => $caretakerExtPath . 'classes/services/tests/class.tx_caretaker_TestServiceBase.php',
		'tx_caretaker_testservicerunner' => $caretakerExtPath . 'classes/services/class.tx_caretaker_TestServiceRunner.php',

);
?>