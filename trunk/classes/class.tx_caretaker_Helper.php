<?php 

require_once ('class.tx_caretaker_NodeRepository.php');


class tx_caretaker_Helper {
	
	static function getNode($instancegroupId = false, $instanceId = false, $testgroupId = false, $testId = false){
		
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
	
	static function node2id ($node){
		$id = false;	
		switch (get_class ($node)){
			case 'tx_caretaker_Instancegroup':
				$id = 'instancegroup_'.$node->getUid();
				break;
			case 'tx_caretaker_Instance':
				$id = 'instance_'.$node->getUid();
				break;
			case 'tx_caretaker_Testgroup':
				$instance = $node->getInstance();
				$id = 'instance_'.$instance->getUid().'_testgroup_'.$node->getUid();
				break;
			case 'tx_caretaker_Test':
				$instance = $node->getInstance();
				$id = 'instance_'.$instance->getUid().'_test_'.$node->getUid();
				break;	
			
		}
		return $id;
	}
	
	static function id2node ($id_string){
		$parts = explode('_', $id_string);
		$info  = array();
		for($i=0; $i<count($parts);$i +=2 ){
			switch ($parts[$i]){
				case 'instancegroup':
					$info['instancegroup']=(int)$parts[$i+1];
					break;
				case 'instance':
					$info['instance']=(int)$parts[$i+1];
					break;
				case 'testgroup':
					$info['testgroup']=(int)$parts[$i+1];
					break;
				case 'test':
					$info['test']=(int)$parts[$i+1];
					break;
			}
		}
		return tx_caretaker_Helper::getNode($info['instancegroup'],$info['instance'],$info['testgroup'],$info['test'] );
	}
	
}
?>