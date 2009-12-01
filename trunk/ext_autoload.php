<?php
/*
 * Register necessary class names with autoloader
 *
 * $Id$
 */
return array(

	'tx_caretaker_clilogger'		      => t3lib_extMgm::extPath('caretaker', 'classes/class.tx_caretaker_CliLogger.php'),
	'tx_caretaker_clinotifier'		      => t3lib_extMgm::extPath('caretaker', 'classes/class.tx_caretaker_CliNotifier.php'),

		/* helpers */
	'tx_caretaker_servicehelper'          => t3lib_extMgm::extPath('caretaker', 'classes/helpers/class.tx_caretaker_ServiceHelper.php'),
	'tx_caretaker_locallizationhelper'     => t3lib_extMgm::extPath('caretaker', 'classes/helpers/class.tx_caretaker_LocallizationHelper.php'),
	'tx_caretaker_extensionmanagerhelper' => t3lib_extMgm::extPath('caretaker', 'classes/helpers/class.tx_caretaker_ExtensionManagerHelper.php'),

		/* plugins */
	'tx_caretaker_pibase' => t3lib_extMgm::extPath('caretaker', '/pi_base/class.tx_caretaker_pibase.php'),

		/* notification services */
	'tx_caretaker_notificationservicebase'       => t3lib_extMgm::extPath('caretaker', 'classes/services/class.tx_caretaker_NotificationServiceBase.php'),
	'tx_caretaker_simplemailnotificationservice' => t3lib_extMgm::extPath('caretaker', 'classes/services/class.tx_caretaker_SimpleMailNotificationService.php'),

		/* interfaces */
	'tx_caretaker_loggerinterface'		=> t3lib_extMgm::extPath('caretaker', 'interfaces/interface.tx_caretaker_LoggerInterface.php'),
	'tx_caretaker_resultrangerenderer'	=> t3lib_extMgm::extPath('caretaker', 'interfaces/interface.tx_caretaker_ResultRangeRenderer.php'),
	'tx_caretaker_testservice'			=> t3lib_extMgm::extPath('caretaker', 'interfaces/interface.tx_caretaker_TestService.php'),

		/* testrunner */
	'tx_caretaker_testrunnertask'							=> t3lib_extMgm::extPath('caretaker', 'scheduler/class.tx_caretaker_testrunnertask.php'),
	'tx_caretaker_testrunnertask_additionalfieldprovider'	=> t3lib_extMgm::extPath('caretaker', 'scheduler/class.tx_caretaker_testrunnertask_additionalfieldprovider.php'),
	'tx_caretaker_terupdatetask'							=> t3lib_extMgm::extPath('caretaker', 'scheduler/class.tx_caretaker_terupdatetask.php'),
	'tx_caretaker_terupdatetask_additionalfieldprovider'	=> t3lib_extMgm::extPath('caretaker', 'scheduler/class.tx_caretaker_terupdatetask_additionalfieldprovider.php'),
	
		/* nodes */
	'tx_caretaker_abstractnode'	      => t3lib_extMgm::extPath('caretaker', 'classes/nodes/class.tx_caretaker_AbstractNode.php'),
	'tx_caretaker_aggregatornode'	  => t3lib_extMgm::extPath('caretaker', 'classes/nodes/class.tx_caretaker_AggregatorNode.php'),
	'tx_caretaker_instancenode'	      => t3lib_extMgm::extPath('caretaker', 'classes/nodes/class.tx_caretaker_InstanceNode.php'),
	'tx_caretaker_instancegroupnode'  => t3lib_extMgm::extPath('caretaker', 'classes/nodes/class.tx_caretaker_InstancegroupNode.php'),
	'tx_caretaker_rootnode'	          => t3lib_extMgm::extPath('caretaker', 'classes/nodes/class.tx_caretaker_RootNode.php'),
	'tx_caretaker_testnode'	          => t3lib_extMgm::extPath('caretaker', 'classes/nodes/class.tx_caretaker_TestNode.php'),
	'tx_caretaker_testgroupnode'	  => t3lib_extMgm::extPath('caretaker', 'classes/nodes/class.tx_caretaker_TestgroupNode.php'),

		/* repositories */
	'tx_caretaker_aggregatorresultrepository' => t3lib_extMgm::extPath('caretaker', 'classes/repositories/class.tx_caretaker_AggregatorResultRepository.php'),
	'tx_caretaker_testresultrepository'       => t3lib_extMgm::extPath('caretaker', 'classes/repositories/class.tx_caretaker_TestResultRepository.php'),
	'tx_caretaker_noderepository'             => t3lib_extMgm::extPath('caretaker', 'classes/repositories/class.tx_caretaker_NodeRepository.php'),

		/* results */
	'tx_caretaker_noderesult'	         => t3lib_extMgm::extPath('caretaker', 'classes/results/class.tx_caretaker_NodeResult.php'),
	'tx_caretaker_aggregatorresult'	     => t3lib_extMgm::extPath('caretaker', 'classes/results/class.tx_caretaker_AggregatorResult.php'),
	'tx_caretaker_testresult'	         => t3lib_extMgm::extPath('caretaker', 'classes/results/class.tx_caretaker_TestResult.php'),
	'tx_caretaker_noderesultrange'	     => t3lib_extMgm::extPath('caretaker', 'classes/results/class.tx_caretaker_NodeResultRange.php'),
	'tx_caretaker_aggregatorresultrange' => t3lib_extMgm::extPath('caretaker', 'classes/results/class.tx_caretaker_AggregatorResultRange.php'),
	'tx_caretaker_testresultrange'	     => t3lib_extMgm::extPath('caretaker', 'classes/results/class.tx_caretaker_TestResultRange.php'),
	'tx_caretaker_resultmessage'         => t3lib_extMgm::extPath('caretaker', 'classes/results/class.tx_caretaker_ResultMessage.php'),

		/* services */
	'tx_caretaker_testservicebase'	     => t3lib_extMgm::extPath('caretaker', 'services/class.tx_caretaker_TestServiceBase.php'),

);
?>
