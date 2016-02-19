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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

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
	 * cURL options for this instance
	 *
	 * @var array
	 */
	protected $curlOptions;

	/**
	 *
	 * True, if the new configuration override mechanism is enabled
	 * @var bool
	 */
	protected $newConfigurationOverrideEnabled = FALSE;

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
		// check if the new configuration overrides are enabled
		$extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
		$this->newConfigurationOverrideEnabled = $extConfig['features.']['newConfigurationOverrides.']['enabled'] == '1';
		if(VersionNumberUtility::convertVersionNumberToInteger(VersionNumberUtility::getCurrentTypo3Version()) >= VersionNumberUtility::convertVersionNumberToInteger('7.5.0')) {
			// enable new configurations overrides automatically with 7.5 and later
			$this->newConfigurationOverrideEnabled = TRUE;
		}
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
	 * @return string
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
	 * @see tx_caretaker_AggregatorNode#findChildren()
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
		if($this->newConfigurationOverrideEnabled) {
			$this->testConfigurationOverlay = $data;
		} else {
			$this->testConfigurationOverlay = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($data);
		}
	}

	public function setCurlOptions($curlOptions) {
		$this->curlOptions = $curlOptions;
	}

	/**
	 * @return array
	 */
	public function getCurlOptions() {
		$curl_options = array();
		if($this->newConfigurationOverrideEnabled) {
            if (is_array($this->curlOptions)) {
                foreach ($this->curlOptions as $option) {
                    $value = NULL;
                    switch ($option['curl_option']) {
                        case 'CURLOPT_SSL_VERIFYPEER':
                            $value = (boolean)($option['curl_value_bool'] != 'false');
                            break;

                        case 'CURLOPT_TIMEOUT_MS':
                            $value = intval($option['curl_value_int']);
                            break;

                        case 'CURLOPT_INTERFACE':
                            $value = $option['curl_value_string'];
                            break;

                        case 'CURLOPT_USERPWD':
                            $value = $option['curl_value_string'];
                            break;

                        case 'CURLOPT_HTTPAUTH':
                            $value = intval($option['curl_value_httpauth']);
                            break;
                    }
                    $curl_options[constant($option['curl_option'])] = $value;
                }
            }
		} else {
			if ($this->testConfigurationOverlay) {
				$fftools = new \TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools();
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
									$value = (boolean)($currentEl['value_bool']['vDEF'] != 'false');
									break;

								case 'CURLOPT_TIMEOUT_MS':
									$value = intval($currentEl['value_int']['vDEF']);
									break;

								case 'CURLOPT_INTERFACE':
									$value = (string)$currentEl['value_ip']['vDEF'];
									break;

								case 'CURLOPT_USERPWD':
									$value = (string)$currentEl['value_string']['vDEF'];
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
		}

		return $curl_options;
	}

	/**
	 * Get the test configuration overlay (configuration overwritten in instance)
	 *
	 * @param integer $testUid UID of the test
	 * @return array
	 */
	public function getTestConfigurationOverlayForTestUid($testUid) {
		$overlayConfig = FALSE;
		if($this->newConfigurationOverrideEnabled) {
            if(is_array($this->testConfigurationOverlay)) {
                foreach ($this->testConfigurationOverlay as $configurationOverlay) {
                    if ($configurationOverlay['test'] == $testUid) {
                        $overlayConfig = GeneralUtility::xml2array($configurationOverlay['test_configuration']);
                        $overlayConfig['hidden'] = $configurationOverlay['test_hidden'];
                        $overlayConfig['overwritten_in']['title'] = $this->title;
                        $overlayConfig['overwritten_in']['uid'] = $this->uid;
                        $overlayConfig['overwritten_in']['id'] = $this->getCaretakerNodeId();
                    }
                }
            }
		} else {
			if ($this->testConfigurationOverlay) {
				$fftools = new \TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools();
				$tests = $fftools->getArrayValueByPath(
					'data/sDEF/lDEF/testconfigurations/el',
					$this->testConfigurationOverlay
				);
				if (is_array($tests)) {
					foreach ($tests as $key => $el) {
						if ($tests[$key]['test']['el']['test_service']['vDEF'] == $testUid) {
							$overlayConfig = $tests[$key]['test']['el']['test_conf']['vDEF'];
							$overlayConfig['hidden'] = $tests[$key]['test']['el']['test_hidden']['vDEF'];
							$overlayConfig['overwritten_in']['title'] = $this->title;
							$overlayConfig['overwritten_in']['uid'] = $this->uid;
							$overlayConfig['overwritten_in']['id'] = $this->getCaretakerNodeId();
						}
					}
				}
			}
		}
		if (!$overlayConfig) {
			$overlayConfig = parent::getTestConfigurationOverlayForTestUid($testUid);
		}

		return $overlayConfig;
	}
}
