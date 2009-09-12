<?php

require_once (t3lib_extMgm::extPath('caretaker') . '/classes/repositories/class.tx_caretaker_NodeRepository.php');
require_once (t3lib_extMgm::extPath('caretaker') . '/classes/class.tx_caretaker_Helper.php');

class tx_caretaker_treeloader {
	public function ajaxLoadTree($params, &$ajaxObj) {
		$node_repository = tx_caretaker_NodeRepository::getInstance();
		
		$rootnode = $node_repository->getRootNode(true);

		$result = $this->nodeToArray($rootnode);
		
		$ajaxObj->setContent($result['children']);

		$ajaxObj->setContentFormat('json');
	}
	
	protected function nodeToArray($node, $level = 0) {
			// show node and icon
		$result = array();
		$uid    = $node->getUid();
		$title  = $node->getTitle();
		$hidden = $node->getHidden();
		$table  = 'tx_caretaker_' . strToLower($node->getType());
		
		$id = tx_caretaker_Helper::node2id($node);
		
		$testResult = $node->getTestResult();
		$resultClass = 'caretaker-state-' . $testResult->getState();
		$typeClass = 'caretaker-type-' . strtolower($node->getType());
	
		$result['id'] = $id;
		$result['text'] = $title;
		$result['cls'] = $resultClass . ' ' . $typeClass;
		$result['iconCls'] = 'icon-' . $typeClass;
		
			// show subitems of tx_caretaker_AggregatorNodes
		if (is_a($node, 'tx_caretaker_AggregatorNode')) {
			$result['children'] = array();
			$children = $node->getChildren(true);
			if (count($children) > 0) {
				foreach($children as $child){
					$result['children'][] = $this->nodeToArray($child, $level + 1) ;
				}
			}
		} else {
			$result['leaf'] = TRUE;
		}
	
		return $result;
	}	
}
?>