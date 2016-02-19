<?php

namespace Caretaker\Caretaker\Command;


use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

class MigrationCommandController extends CommandController {
	public function configurationOverridesCommand() {
		foreach($GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid,testconfigurations', 'tx_caretaker_instance', 'deleted=0') as $instance) {
			if (!empty($instance['testconfigurations'])) {
				$xml = GeneralUtility::xml2array($instance['testconfigurations']);
				if (is_array($xml)) {
					foreach($xml['data']['sDEF']['lDEF']['testconfigurations']['el'] as $section) {
						if (array_key_exists('test', $section)) {
							$configurationOverrideRecord = array(
								'type' => 'test_configuration',
								'tstamp' => time(),
								'crdate' => time(),
								'cruser_id' => (int)$GLOBALS['BE_USER']->user['uid'],
								'instance' => (int)$instance['uid']
							);
							foreach($section['test']['el'] as $fieldName => $fieldData) {
								switch ($fieldName) {
									case 'test_service':
										$configurationOverrideRecord['test'] = (int)$fieldData['vDEF'];
										break;
									case 'test_hidden':
										$configurationOverrideRecord['test_hidden'] = $fieldData['vDEF'];
										break;
									case 'test_conf':
										$configurationOverrideRecord['test_configuration'] = GeneralUtility::array2xml($fieldData['vDEF'], '', 0, 'T3FlexForms');
										break;
								}
							}
							$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_caretaker_instance_override', $configurationOverrideRecord, array('tstamp', 'crdate', 'cruser_id', 'instance', 'test'));
						} elseif (array_key_exists('curl_option', $section)) {
							$configurationOverrideRecord = array(
								'type' => 'curl_option',
								'tstamp' => time(),
								'crdate' => time(),
								'cruser_id' => (int)$GLOBALS['BE_USER']->user['uid'],
								'instance' => (int)$instance['uid']
							);
							foreach($section['curl_option']['el'] as $fieldName => $fieldData) {
								switch ($fieldName) {
									case 'option':
										$configurationOverrideRecord['curl_option'] = $fieldData['vDEF'];
										break;
									case 'value_bool':
										$configurationOverrideRecord['curl_value_bool'] = $fieldData['vDEF'];
										break;
									case 'value_int':
										$configurationOverrideRecord['curl_value_int'] = $fieldData['vDEF'];
										break;
									case 'value_ip':
									case 'value_string':
										$configurationOverrideRecord['curl_value_string'] = $fieldData['vDEF'];
										break;
									case 'value_httpauth':
										$configurationOverrideRecord['curl_value_httpauth'] = $fieldData['vDEF'];
										break;
								}
							}
							$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_caretaker_instance_override', $configurationOverrideRecord, array('tstamp', 'crdate', 'cruser_id', 'instance'));
						}
					}
				}
			}
		}
	}
}
