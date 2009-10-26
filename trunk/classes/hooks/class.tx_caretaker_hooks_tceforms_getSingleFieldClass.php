<?php

class tx_caretaker_hooks_tceforms_getSingleFieldClass {
	function getSingleField_beforeRender($table, $field, &$row, &$PA) {
		global $TCA;
		
		if ($table == 'tx_caretaker_instance' && $field === 'testconfigurations') {
			
			switch ($PA['fieldConf']['config']['tag']) {
				
				case 'testconfigurations.test_service':
					/* * /
					if (count($PA['fieldConf']['config']['items']) != count($TCA['tx_caretaker_test']['columns']['test_service']['config']['items'])) {
						t3lib_div::loadTCA('tx_caretaker_test');
						$PA['fieldConf']['config']['items'] = $TCA['tx_caretaker_test']['columns']['test_service']['config']['items'];
					}
					// */
					break;
					
				case 'testconfigurations.test_conf':
					$test = $this->getFFValue($table, $row, $field, str_replace('test_conf', 'test_service', $PA['itemFormElName']));
					
					// get related test configuration
					$testrow = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
						'uid,test_service,test_conf',
						'tx_caretaker_test',
						'uid=' . intval($test) . t3lib_BEfunc::deleteClause('tx_caretaker_test')	###NOTE_A###
					);
					
					$row['test_service'] = $testrow[0]['test_service'];
					if ($PA['itemFormElValue'] == NULL) {
						$PA['itemFormElValue'] = $testrow[0]['test_conf'];
					}
					if (is_array($PA['itemFormElValue'])) {
						$PA['itemFormElValue'] = t3lib_div::array2xml($PA['itemFormElValue']);
					}
					
					if (!is_array($PA['fieldConf']['config']['ds'])) {
						t3lib_div::loadTCA('tx_caretaker_test');
						$PA['fieldConf']['config']['ds'] = $TCA['tx_caretaker_test']['columns']['test_conf']['config']['ds'];
					}
					// var_dump($PA['fieldConf']['config']['ds']);
					
					
					/* * /
					$row['test_service'] = $test;
					if (!is_array($PA['fieldConf']['config']['ds'])) {
						t3lib_div::loadTCA('tx_caretaker_test');
						$PA['fieldConf']['config']['ds'] = $TCA['tx_caretaker_test']['columns']['test_conf']['config']['ds'];
					}
					// FIXME
					$PA['itemFormElValue']='';
					// */
					break;
			}
		}	
    }
    
    
    protected function getFFValue($table, $row, $field, $itemFormElName) {
    	$path = str_replace('data['. $table . ']['. $row['uid'] .']['. $field .'][', '', $itemFormElName);
		$path = rtrim($path, ']');
		$path = explode('][', $path);
		$fftools = new t3lib_flexformtools();
		$val = $fftools->getArrayValueByPath($path, t3lib_div::xml2array($row[$field]));
		return $val;
    }
	
}