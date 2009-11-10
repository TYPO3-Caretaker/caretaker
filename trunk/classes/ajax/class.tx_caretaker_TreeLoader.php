<?php

class tx_caretaker_TreeLoader {
	
	public function ajaxLoadTree($params, &$ajaxObj) {
		
		$node_id = t3lib_div::_GP('node');
		
		if ( $node_id == 'root'){
			$node_repository = tx_caretaker_NodeRepository::getInstance();
			$node = $node_repository->getRootNode(true);
			$result = $this->nodeToArray($node, 2);
		} else {
			$node =  tx_caretaker_Helper::id2node($node_id, 1);
			$result = $this->nodeToArray($node);
		}
		
		
		
		$ajaxObj->setContent($result['children']);

		$ajaxObj->setContentFormat('jsonbody');
	}
	
	protected function nodeToArray($node, $depth = 1) {
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
	
		$result['type'] = strtolower($node->getType());
		$result['id'] = $id;
		$result['uid'] = $uid;
		$result['disabled'] = $hidden;
		$result['text'] = $title ? $title : '[no title]';
		$result['cls'] = $resultClass . ' ' . $typeClass;
		$result['iconCls'] = 'icon-' . $typeClass . ($hidden ? '-hidden' : '');
		
			// show subitems of tx_caretaker_AggregatorNodes
		if (is_a($node, 'tx_caretaker_AggregatorNode')) {
			$result['leaf'] = FALSE;
			if ($depth > 0){
				$children = $node->getChildren(true);
				$result['children'] = array();
				foreach($children as $child){
					$result['children'][] = $this->nodeToArray($child, $depth - 1 ) ;
				}
			}
		} else {
			$result['leaf'] = TRUE;
		}
	
		return $result;
	}	
}
?>