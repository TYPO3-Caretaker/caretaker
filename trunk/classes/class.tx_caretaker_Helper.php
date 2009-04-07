<?php 

require_once ('class.tx_caretaker_InstancegroupRepository.php');
require_once ('class.tx_caretaker_InstanceRepository.php');
require_once ('class.tx_caretaker_TestgroupRepository.php');
require_once ('class.tx_caretaker_TestRepository.php');

class tx_caretaker_Helper {
	
	static function getNode($instancegroupId, $instanceId, $testgroupId, $testId){

		$instancegroupId = (int)$instancegroupId;
		$instanceId      = (int)$instanceId;
		$testgroupId     = (int)$testgroupId;
		$testId          = (int)$testId;
		
		if ($instancegroupId>0){
			$instancegroup_repoistory    = tx_caretaker_InstancegroupRepository::getInstance();
			$instancegroup = $instancegroup_repoistory->getByUid($instancegroupId, false);
			if ($instancegroup) return $instancegroup;
		} else if ($instanceId>0){
			$instance_repoistory    = tx_caretaker_InstanceRepository::getInstance();
			$instance = $instance_repoistory->getByUid($instanceId, false);
			if ($instance) {
				if ($testgroupId>0){
	    			$group_repoistory    = tx_caretaker_TestgroupRepository::getInstance();
					$group = $group_repoistory->getByUid($testgroupId, $instance);
					if ($group) return $group;		
	    		} else if ($testId>0) {
	    			$test_repoistory    = tx_caretaker_TestRepository::getInstance();
					$test = $test_repoistory->getByUid($testId, $instance);
					if ($test) return $test;		
	    		} else {
					return $instance;		
				}
			}
		} 
		return false;
	}
	
	static function findNodePath($instancegroupId, $instanceId, $testgroupId, $testId){
			
	}
	
}
?>