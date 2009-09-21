<?php
class tx_caretaker_TestrunnerTask_AdditionalFieldProvider implements tx_scheduler_AdditionalFieldProvider {

	/**
	 * This method is used to define new fields for adding or editing a task
	 * In this case, it adds an email field
	 *
	 * @param	array					$taskInfo: reference to the array containing the info used in the add/edit form
	 * @param	object					$task: when editing, reference to the current task object. Null when adding.
	 * @param	tx_scheduler_Module		$parentObject: reference to the calling object (Scheduler's BE module)
	 * @return	array					Array containg all the information pertaining to the additional fields
	 *									The array is multidimensional, keyed to the task class name and each field's id
	 *									For each field it provides an associative sub-array with the following:
	 *										['code']		=> The HTML code for the field
	 *										['label']		=> The label of the field (possibly localized)
	 *										['cshKey']		=> The CSH key for the field
	 *										['cshLabel']	=> The code of the CSH label
	 */
	public function getAdditionalFields(array &$taskInfo, $task, tx_scheduler_Module $parentObject) {
	/*
			// Initialize extra field value
		if (empty($taskInfo['email'])) {
			if ($parentObject->CMD == 'add') {
					// In case of new task and if field is empty, set default email address
				$taskInfo['email'] = $GLOBALS['BE_USER']->user['email'];

			} elseif ($parentObject->CMD == 'edit') {
					// In case of edit, and editing a test task, set to internal value if not data was submitted already
				$taskInfo['email'] = $task->email;
			} else {
					// Otherwise set an empty value, as it will not be used anyway
				$taskInfo['email'] = '';
			}
		}

			// Write the code for the field
		$fieldID = 'task_email';
		$fieldCode = '<input type="text" name="tx_scheduler[email]" id="' . $fieldID . '" value="' . $taskInfo['email'] . '" size="30" />';
		$additionalFields = array();
		$additionalFields[$fieldID] = array(
			'code'     => $fieldCode,
			'label'    => 'LLL:EXT:scheduler/mod1/locallang.xml:label.email',
			'cshKey'   => '_MOD_tools_txschedulerM1',
			'cshLabel' => $fieldID
		);
		*/
		return $additionalFields;
	}

	/**
	 * This method checks any additional data that is relevant to the specific task
	 * If the task class is not relevant, the method is expected to return true
	 *
	 * @param	array					$submittedData: reference to the array containing the data submitted by the user
	 * @param	tx_scheduler_Module		$parentObject: reference to the calling object (Scheduler's BE module)
	 * @return	boolean					True if validation was ok (or selected class is not relevant), false otherwise
	 */
	public function validateAdditionalFields(array &$submittedData, tx_scheduler_Module $parentObject) {
		/*
		$submittedData['email'] = trim($submittedData['email']);

		if (empty($submittedData['email'])) {
			$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:scheduler/mod1/locallang.xml:msg.noEmail'), t3lib_FlashMessage::ERROR);
			$result = false;
		} else {
			$result = true;
		}

		return $result;
		 */
	}

	/**
	 * This method is used to save any additional input into the current task object
	 * if the task class matches
	 *
	 * @param	array				$submittedData: array containing the data submitted by the user
	 * @param	tx_scheduler_Task	$task: reference to the current task object
	 * @return	void
	 */
	public function saveAdditionalFields(array $submittedData, tx_scheduler_Task $task) {
		// $task->email = $submittedData['email'];
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/scheduler/class.tx_caretaker_testrunnertask_additionalfieldprovider.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/scheduler/class.tx_caretaker_testrunnertask_additionalfieldprovider.php']);
}

?>