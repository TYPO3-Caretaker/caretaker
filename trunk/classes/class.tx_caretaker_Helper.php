<?php 

require_once ('class.tx_caretaker_NodeRepository.php');


class tx_caretaker_Helper {
	
	static function getNode($instancegroupId, $instanceId, $testgroupId, $testId){
		
		$node_repoistory    = tx_caretaker_NodeRepository::getInstance();

		$instancegroupId = (int)$instancegroupId;
		$instanceId      = (int)$instanceId;
		$testgroupId     = (int)$testgroupId;
		$testId          = (int)$testId;
		
		if ($instancegroupId>0){
			$instancegroup = $node_repoistory->getInstancegroupByUid($instancegroupId, false);
			if ($instancegroup) return $instancegroup;
		} else if ($instanceId>0){
			$instance = $node_repoistory->getInstanceByUid($instanceId, false);
			if ($instance) {
				if ($testgroupId>0){
					$group = $node_repoistory->getTestgroupByUid($testgroupId, $instance);
					if ($group) return $group;		
	    		} else if ($testId>0) {
					$test = $node_repoistory->getTestByUid($testId, $instance);
					if ($test) return $test;		
	    		} else {
					return $instance;		
				}
			}
		} 
		return false;
	}
	
}
?>