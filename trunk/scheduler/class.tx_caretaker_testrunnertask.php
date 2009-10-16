<?php

class tx_caretaker_TestrunnerTask extends tx_scheduler_Task {

	private $node_id;


	public function setNodeId($id){
		$this->node_id = $id;
	}

	public function getNodeId(){
		return $this->node_id;
	}

	public function execute() {
		

		require_once (t3lib_extMgm::extPath('caretaker') . '/classes/class.tx_caretaker_Helper.php');
		require_once (t3lib_extMgm::extPath('caretaker') . '/classes/class.tx_caretaker_CliLogger.php');
		require_once (t3lib_extMgm::extPath('caretaker') . '/classes/class.tx_caretaker_CliNotifier.php');

		$node = tx_caretaker_Helper::id2node($this->node_id);

		if (!$node)return false;

		$notifier = new tx_caretaker_CliNotifier();
		$node->setNotifier($notifier);

		$node->updateTestResult();

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