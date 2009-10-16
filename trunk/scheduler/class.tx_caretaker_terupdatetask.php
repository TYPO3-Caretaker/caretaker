<?php

class tx_caretaker_TerupdateTask extends tx_scheduler_Task {


	public function execute() {
		$success = false;
		require_once (t3lib_extMgm::extPath('caretaker') . '/classes/class.tx_caretaker_ExtensionManagerHelper.php');
		tx_caretaker_ExtensionManagerHelper::updateExtensionList();
		$success = true;
		return $success;
	}

	public function getAdditionalInformation() {
		// return $GLOBALS['LANG']->sL('LLL:EXT:scheduler/mod1/locallang.xml:label.email') . ': ' . $this->email;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/scheduler/class.tx_caretaker_testrunnertask.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/scheduler/class.tx_caretaker_testrunnertask.php']);
}

?>