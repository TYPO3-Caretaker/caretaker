<?php

require_once (t3lib_extMgm::extPath('caretaker') . '/classes/repositories/class.tx_caretaker_NodeRepository.php');
require_once (t3lib_extMgm::extPath('caretaker') . '/classes/class.tx_caretaker_Helper.php');

class tx_caretaker_NodeInfo {

	public function ajaxGetNodeInfo($params, &$ajaxObj){
		
		$node_id = t3lib_div::_GP('node');

		if ($node_id && $node = tx_caretaker_Helper::id2node($node_id, true) ){

			$local_time = localtime(time(), true);
			$local_hour = $local_time['tm_hour'];

			switch ( get_class($node) ){
				// test Node
				case "tx_caretaker_TestNode":

					$interval_info = '';
					$interval = $node->getInterval();
					if ( $interval < 60){
						$interval_info .= $interval.' Seconds';
					} else if ($interval < 60*60){
						$interval_info .= ($interval/60).' Minutes';
					} else if ($interval < 60*60*24){
						$interval_info .= ($interval/(60*60)).' Hours';
					} else {
						$interval_info .= ($interval/86400).' Days';
					}

					if ($node->getStartHour() || $node->getStopHour() >0){
						$interval_info .= ' [';
						if ($node->getStartHour() )
							$interval_info .= ' after:'.$node->getStartHour();
						if ($node->getStopHour() )
							$interval_info .= ' before:'.$node->getStopHour();
						$interval_info .= ' ]';
					}

					$result = $node->getTestResult();
					$info = '<div class="tx_caretaker_node_info tx_caretaker_node_info_state_'.$result->getStateInfo().'">'.
						'Title: '.           $node->getTitle().'<br/>'.
						'NodeID: '.          tx_caretaker_Helper::node2id($node).'<br/>'.
						'Type: '.            $node->getTypeDescription().'<br/>'.
						'Interval: '.        $interval_info.'<br/>'.
						'Description: '.     $node->getDescription().'<br/>'.
						'Configuration: '.   $node->getConfigurationInfo().'<br/>'.
						'Hidden: '.          $node->getHiddenInfo() .'<br/>'.
						'last Execution: '.  strftime('%x %X',$result->getTimestamp()).'<br/>'.
						'State: '.           $result->getLocallizedStateInfo().'<br/>'.
						'Value: '.           $result->getValue().'<br/>'.
						'Message: <br/>'.nl2br( str_replace( ' ' , '&nbsp;', $result->getLocallizedMessage() ) ) .'<br/>'.
						'</div>';
					break;
				default:
					// aggregator Node
					$result = $node->getTestResult();
					$info = '<div class="tx_caretaker_node_info tx_caretaker_node_info_state_'.$result->getStateInfo().'">'.
						'Title: '.           $node->getTitle().'<br/>'.
						'NodeID: '.          tx_caretaker_Helper::node2id($node).'<br/>'.
						'Description: '.     $node->getDescription().'<br/>'.
						'Hidden: '.          $node->getHiddenInfo().'<br/>'.
						'last Execution: '.  strftime('%x %X',$result->getTimestamp()).'<br/>'.
						'State: '.           $result->getLocallizedStateInfo().'<br/>'.
						'Message: '.         nl2br($result->getLocallizedMessage()).'<br/>'.
						'</div>';
					break;
				}

			echo $info;

		} else {
			echo "please select a node";

		}
		
	}

	public function ajaxRefreshNode($params, &$ajaxObj){

		$node_id = t3lib_div::_GP('node');
		$force   = (boolean)t3lib_div::_GP('force');

		if ($node_id && $node = tx_caretaker_Helper::id2node($node_id, true) ){

			require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_MemoryLogger.php');
			$logger  = new tx_caretaker_MemoryLogger();

			$node->setLogger($logger);
			$node->updateTestResult($force);

			echo nl2br($logger->getLog());
			
		} else {
			echo "please give a valid node id";
		}
	}

	public function ajaxGetNodeGraph($params, &$ajaxObj){

		$node_id    = t3lib_div::_GP('node');
		
		$duration   = (int)t3lib_div::_GP('duration');
		$date_stop  = time();
		$date_start = $date_stop - $duration;

		if ($node_id && $node = tx_caretaker_Helper::id2node($node_id, true) ){

			require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_ResultRangeRenderer_pChart.php');

			$result_range = $node->getTestResultRange($date_start , $date_stop);

			if ($result_range->count() > 1 ){
				$filename = 'typo3temp/caretaker/charts/'.$node_id.'_'.$duration.'.png';
				$renderer = tx_caretaker_ResultRangeRenderer_pChart::getInstance();
				$base_url = t3lib_div::getIndpEnv('TYPO3_SITE_URL');

				if (is_a($node, 'tx_caretaker_TestNode' ) ){
					if ($renderer->renderTestResultRange(PATH_site.$filename, $result_range , $node->getTitle(), $node->getValueDescription()) !== false) {
						echo '<img src="'.$base_url.$filename.'?random='.rand().'" />';
					}
				} else  if (is_a( $node, 'tx_caretaker_AggregatorNode')){
					if ($renderer->renderAggregatorResultRange(PATH_site.$filename, $result_range , $node->getTitle()) !== false) {
						echo '<img src="'.$base_url.$filename.'?random='.rand().'" />';
					}
				}
			} else {
				echo 'not enough results';
			}

		} else {
			echo "please give a valid node id";
		}
	}

    public function ajaxGetNodeLog ($params, &$ajaxObj){

        $node_id = t3lib_div::_GP('node');

        if ($node_id && $node = tx_caretaker_Helper::id2node($node_id, true) ){
            
            $start     = (int)t3lib_div::_GP('start');
            $limit     = (int)t3lib_div::_GP('limit');

            $count   = $node->getTestResultNumber();
            $results = $node->getTestResultRangeByOffset($start, $limit);
            
            $content = Array(
                'totalCount' => $count,
                'logItems' => Array()
            );

            $logItems = array();
            foreach ($results as $result){
                $logItems[] = Array (
                    'num'=> ($i+1) ,
                    'title'=>'title_'.rand(),
                    'timestamp' => $result->getTimestamp(),
					'stateinfo' => $result->getStateInfo(),
                    'stateinfo_ll' => $result->getLocallizedStateInfo(),
					'message'   => $result->getMessage(),
                    'message_ll'   => $result->getLocallizedMessage(),
                    'state'     => $result->getState(),
                );
            }
            $content['logItems'] = array_reverse($logItems);


            $ajaxObj->setContent($content);
            $ajaxObj->setContentFormat('jsonbody');
        }
    }
	
	
}
?>
