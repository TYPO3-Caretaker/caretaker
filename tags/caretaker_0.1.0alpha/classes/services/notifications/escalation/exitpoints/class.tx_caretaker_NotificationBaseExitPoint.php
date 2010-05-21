<?php

class tx_caretaker_NotificationBaseExitPoint implements tx_caretaker_NotificationExitPointInterface {

	protected $config = array();

	public function init(array $config) {
		$this->config = $config;
	}

	public function execute() {

	}
}

?>