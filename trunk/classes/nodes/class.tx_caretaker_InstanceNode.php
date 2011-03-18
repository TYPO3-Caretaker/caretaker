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
 * Caretaker-node which represents an monitored website or TYPO3-installation.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_InstanceNode extends tx_caretaker_AggregatorNode {

	/**
	 * URL to access this instance
	 * @var string
	 */
	protected $url;

	/**
	 * Hostname
	 * @var string
	 */
	protected $hostname;

	/**
	 * Public key
	 * @var string
	 */
	protected $publicKey;

	/**
	 * test configuration overlay to overwrite tests default configurations
	 * @var array
	 */
	protected $testConfigurationOverlay;

	/**
	 * Constructor
	 *
	 * @param integer $uid
	 * @param string $title
	 * @param tx_caretaker_AbstractNode $parent
	 * @param string $url
	 * @param string $host
	 * @param string $ip
	 * @param boolean $hidden
	 */
	public function __construct($uid, $title, $parent, $url = '', $hostname = '', $publicKey = '', $hidden = 0) {
		parent::__construct($uid, $title, $parent, tx_caretaker_Constants::table_Instances, tx_caretaker_Constants::nodeType_Instance, $hidden);
		$this->url = $url;
		$this->hostname = $hostname;
		$this->publicKey = $publicKey;
	}

	/**
	 * Get the caretaker node id of this node
	 * return string
	 */
	public function getCaretakerNodeId() {
		return 'instance_' . $this->getUid();
	}

	/**
	 * Get the url
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * Get the hostname
	 * @return unknown_type
	 */
	public function getHostname() {
		return $this->hostname;
	}

	/**
	 * Get the public key
	 * @return string
	 */
	public function getPublicKey() {
		return $this->publicKey;
	}

	/**
	 * Find Child nodes
	 * @param boolean $show_hidden
	 * @return array
	 * @see caretaker/trunk/classes/nodes/tx_caretaker_AggregatorNode#findChildren()
	 */
	public function findChildren($show_hidden = false) {
		$node_repository = tx_caretaker_NodeRepository::getInstance();
		$testgroups = $node_repository->getTestgroupsByInstanceUid($this->uid, $this, $show_hidden);
		$tests = $node_repository->getTestsByInstanceUid($this->uid, $this, $show_hidden);
		return array_merge($testgroups, $tests);
	}

	/**
	 * @param string $data XML
	 * @return void
	 */
	public function setTestConfigurations($data) {
		$this->testConfigurationOverlay = t3lib_div::xml2array($data);
	}

	/**
	 * @return array
	 */
	public function getCurlOptions() {
		$curl_options = array();
		if ($this->testConfigurationOverlay) {
			$fftools = new t3lib_flexformtools();
			$options = $fftools->getArrayValueByPath(
				'data/sDEF/lDEF/testconfigurations/el',
				$this->testConfigurationOverlay
			);
			if ($options && is_array($options)) {
				foreach ($options as $key => $el) {
					if (is_array($el['curl_option'])) {
						$currentEl = $el['curl_option']['el'];
						$value = '';
						if (!defined($currentEl['option']['vDEF'])) {
							continue;
						}
						switch ($currentEl['option']['vDEF']) {
							case 'CURLOPT_SSL_VERIFYPEER':
								$value = (boolean) ($currentEl['value_bool']['vDEF'] != 'false');
								break;

							case 'CURLOPT_TIMEOUT_MS':
								$value = intval($currentEl['value_int']['vDEF']);
								break;

							case 'CURLOPT_INTERFACE':
								$value = (string) $currentEl['value_ip']['vDEF'];
								break;

							case 'CURLOPT_USERPWD':
								$value = (string) $currentEl['value_string']['vDEF'];
								break;

							case 'CURLOPT_HTTPAUTH':
								$value = intval($currentEl['value_httpauth']['vDEF']);
								break;
						}
						$curl_options[constant($currentEl['option']['vDEF'])] = $value;
					}
				}
			}
		}
		return $curl_options;
	}

}
?>
