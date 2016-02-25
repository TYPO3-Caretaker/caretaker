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
use TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface;

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
class tx_caretaker_TestrunnerTask_AdditionalFieldProvider implements AdditionalFieldProviderInterface {

	/**
	 * This method is used to define new fields for adding or editing a task
	 * In this case, it adds an email field
	 *
	 * @param    array $taskInfo : reference to the array containing the info used in the add/edit form
	 * @param    object $task : when editing, reference to the current task object. Null when adding.
	 * @param     \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject : reference to the calling object (Scheduler's BE module)
	 * @return    array                    Array containg all the information pertaining to the additional fields
	 *                                    The array is multidimensional, keyed to the task class name and each field's id
	 *                                    For each field it provides an associative sub-array with the following:
	 *                                        ['code']        => The HTML code for the field
	 *                                        ['label']        => The label of the field (possibly localized)
	 *                                        ['cshKey']        => The CSH key for the field
	 *                                        ['cshLabel']    => The code of the CSH label
	 */
	public function getAdditionalFields(array &$taskInfo, $task, \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject) {
		if ($task != null && !is_a($task, 'tx_caretaker_TestrunnerTask')) return NULL;

		// Initialize extra field value
		if (empty($taskInfo['update_node_id'])) {
			if ($parentObject->CMD == 'add') {
				// In case of new task and if field is empty, set default email address
				$taskInfo['update_node_id'] = 'root';
			} elseif ($parentObject->CMD == 'edit') {
				// In case of edit, and editing a test task, set to internal value if not data was submitted already
				$taskInfo['update_node_id'] = $task->getNodeId();
			} else {
				// Otherwise set an empty value, as it will not be used anyway
				$taskInfo['update_node_id'] = 'root';
			}
		}

		$fieldID = 'update_node_id';
		$fieldCode = '<input type="text" name="tx_scheduler[' . $fieldID . ']" id="' . $fieldID . '" value="' . $taskInfo['update_node_id'] . '" size="30" />';
		$additionalFields[$fieldID] = array(
				'code' => $fieldCode,
				'label' => 'LLL:EXT:caretaker/locallang.xml:scheduler_update_node',
				'cshKey' => '_MOD_tools_txschedulerM1',
				'cshLabel' => $fieldID
		);

		return $additionalFields;
	}

	/**
	 * This method checks any additional data that is relevant to the specific task
	 * If the task class is not relevant, the method is expected to return true
	 *
	 * @param    array $submittedData : reference to the array containing the data submitted by the user
	 * @param     \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject : reference to the calling object (Scheduler's BE module)
	 * @return    boolean                    True if validation was ok (or selected class is not relevant), false otherwise
	 */
	public function validateAdditionalFields(array &$submittedData,  \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject) {
		$submittedData['update_node_id'] = trim($submittedData['update_node_id']);

		if (empty($submittedData['update_node_id'])) {
			$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:caretaker/locallang.xml:scheduler_update_node_required'), \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
			$result = false;
		} else {
			$result = true;
		}

		return $result;
	}

	/**
	 * This method is used to save any additional input into the current task object
	 * if the task class matches
	 *
	 * @param array $submittedData : array containing the data submitted by the user
	 * @param tx_caretaker_TestrunnerTask|\TYPO3\CMS\Scheduler\Task\AbstractTask $task : reference to the current task object
	 */
	public function saveAdditionalFields(array $submittedData, \TYPO3\CMS\Scheduler\Task\AbstractTask $task) {
		$task->setNodeId($submittedData['update_node_id']);
	}
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/caretaker/scheduler/class.tx_caretaker_testrunnertask_additionalfieldprovider.php']) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/caretaker/scheduler/class.tx_caretaker_testrunnertask_additionalfieldprovider.php']);
}

