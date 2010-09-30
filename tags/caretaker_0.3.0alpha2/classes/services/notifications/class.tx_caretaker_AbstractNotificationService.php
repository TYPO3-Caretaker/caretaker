<?php

class tx_caretaker_AbstractNotificationService implements tx_caretaker_NotificationServiceInterface {

	/**
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * @var array
	 */
	protected $notificationQueue = array();

	/**
	 *
	 * @var array
	 */
	protected $extConfig = array();

	public function __construct($serviceKey = '') {
		// var_dump('=======> construct '.$serviceKey);
		$this->extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
		$this->setId($serviceKey);
	}

	/**
	 *
	 * @param string $id
	 */
	public function setId($id) {
		$this->id = str_replace(' ', '', strtolower(trim($id)));
	}

	/**
	 *
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 *
	 * @param mixed $notification
	 * @param string $key
	 */
	public function addNotificationToQueue($notification, $key = null) {
		$this->notificationQueue[$key] = $notification;
	}

	public function getNotificationFromQueue($key = null) {
		if ($key != null && isset($this->notificationQueue[$key])) {
			return $this->notificationQueue[$key];
		} else {
			return array_pop($this->notificationQueue);
		}
	}

	public function getNotificationQueue() {
		return $this->notificationQueue;
	}

	/**
	 * Returns a value from the extension config array inside the path of the
	 * notification id. e.g. notifications.id.enabled for key "enabled".
	 * Returns NULL if no data was found.
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getConfigValue($key) {
		if (isset($this->extConfig['notifications.'][$this->getId().'.'][$key])) {
			return $this->extConfig['notifications.'][$this->getId().'.'][$key];
		} else {
			return NULL;
		}
	}

	/**
	 * This returns wether the service is enabled or not. By default this returns the value of
	 * the extension config for the key notifications.[id].enabled.
	 * This can be overwritten by the specific implementation of the notification service.
	 *
	 * @return boolean
	 */
	public function isEnabled() {
		$enabled = (bool)$this->getConfigValue('enabled');
		return ($enabled === TRUE && TYPO3_MODE == 'BE' && $GLOBALS["BE_USER"]->user['username'] == '_cli_caretaker');
	}

	public function addNotification($event, $node, $result = NULL, $lastResult = NULL) {}
	
	public function sendNotifications() {}
	
}

?>
