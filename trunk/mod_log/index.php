<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009  <>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Module 'Caretaker' for the 'caretaker' extension.
 *
 * @author	 Martin Ficzel <ficzel@work.de>
 */



	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require ("conf.php");

require ($BACK_PATH."init.php");
require ($BACK_PATH."template.php");
$LANG->includeLLFile("EXT:caretaker/mod_log/locallang.xml");
require_once (PATH_t3lib."class.t3lib_scbase.php");
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_Helper.php');

$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]

class tx_caretaker_mod_log extends t3lib_SCbase {

	var $info;
	
	/**
	 * Initializes the Module
	 * @return	void
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		parent::init();

		$this->id = $_GET['id'];
		
		/*
		 * reads the parameters
		 * if the times are not set the default values are used
		 */
		$this->timeFrom = $_REQUEST['tx_caretaker_mod_log']['timeFrom'];
		$this->timeTo = $_REQUEST['tx_caretaker_mod_log']['timeTo'];
		
		if(empty($this->timeFrom)) {
			
			$this->timeFrom = date('Y-m-d H:i', time()-3600*24); // by default set the range to the last 24 hours
			
		} else {
			
			$this->timeFrom = date('Y-m-d H:i', strtotime($this->timeFrom));
		}
		
		if(empty($this->timeTo)) {
			
			$this->timeTo = date('Y-m-d H:i', time()); // by default set the range to the last 24 hours
			
		} else {
			
			$this->timeTo = date('Y-m-d H:i', strtotime($this->timeTo));
		}
		
		/*
		if (t3lib_div::_GP("clear_all_cache"))	{
			$this->include_once[]=PATH_t3lib."class.t3lib_tcemain.php";
		}
		*/
	}

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	function menuConfig()	{
		global $LANG;
		$this->MOD_MENU = Array (
			"function" => Array (
				"12" =>$LANG->getLL("day"),
				"24"   => $LANG->getLL("today"),
				"72"   => $LANG->getLL("days"),
				"336"  => $LANG->getLL("weeks"),
				"2160" => $LANG->getLL("months"),
			),
		);
		parent::menuConfig();
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	 *
	 * @return	[type]		...
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		//$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		// $access = is_array($this->pageinfo) ? 1 : 0;

	
		
		if ( true /*($this->id && $access) || ($BE_USER->user["admin"] && !$this->id)*/ )	{

				// Draw the header.
			$this->doc = t3lib_div::makeInstance("bigDoc");
			$this->doc->backPath = $BACK_PATH;
			$this->doc->form='<form action="" method="POST">';

				// JavaScript
			$this->doc->JScode = '
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL)	{
						document.location = URL;
					}
					
					function jumpTo(params,linkObj,highLightID)	{ //
						var theUrl = top.TS.PATH_typo3+top.currentSubScript+"?"+params;
						if (top.condensedMode)	{
							top.content.document.location=theUrl;
						} else {
							parent.list_frame.document.location=theUrl;
						}
						'.($this->doHighlight?'hilight_row("row"+top.fsMod.recentIds["txdirectmailM1"],highLightID);':'').'
						'.(!$GLOBALS['CLIENT']['FORMSTYLE'] ? '' : 'if (linkObj) {linkObj.blur();}').'
						return false;
					}
				
				</script>
			';
			
			$this->doc->postCode='
				<script language="javascript" type="text/javascript">
					script_ended = 1;
					if (top.fsMod) top.fsMod.recentIds["web"] = 0;
				</script>
			';

			$headerSection = $this->doc->getHeader("pages",$this->pageinfo,$this->pageinfo["_thePath"])."<br />".$LANG->sL("LLL:EXT:lang/locallang_core.xml:labels.path").": ".t3lib_div::fixed_lgd_pre($this->pageinfo["_thePath"],50);

			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(5);
			//$this->content.=$this->doc->section("",$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,"SET[function]",$this->MOD_SETTINGS["function"],$this->MOD_MENU["function"])));
			$this->content.=$this->doc->divider(5);


			// Render content:
			$this->moduleContent();


			// ShortCut
			//if ($BE_USER->mayMakeShortcut())	{
			//	$this->content.=$this->doc->spacer(20).$this->doc->section("",$this->doc->makeShortcutIcon("id",implode(",",array_keys($this->MOD_MENU)),$this->MCONF["name"]));
			//}

			$this->content.=$this->doc->spacer(10);
		} else {
				// If no access or if ID == zero

			$this->doc = t3lib_div::makeInstance("bigDoc");
			$this->doc->backPath = $BACK_PATH;

			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}
	}


	
	/**
	 * Prints out the module HTML
	 *
	 * @return	void
	 */
	function printContent()	{

		$this->content.=$this->doc->endPage();
		echo $this->content;
	}

	/**
	 * Generates the module content
	 *
	 * @return	void
	 */
	function moduleContent()	{
							
		$node = tx_caretaker_Helper::id2node( $this->id , true);

		if ($node){
			
			$this->content.= ($this->showNodeInfo($node));
		} else {
			$this->content.= $this->doc->section( 'Error:','please select a node');
		}
	}
		
	function showNodeInfo($node){

		$content =  $this->getNodeIcon($node);
		$content .= $this->doc->header( $this->getNodeHeader($node) );
		$content .= $this->doc->section( 'info:',    $this->getNodeInfo($node));
		//$content .= $this->doc->section( 'actions:', $this->getNodeActions($node) );
		
		if (is_a($node, 'tx_caretaker_AggregatorNode')) {
			
			$content .= $this->doc->section( 'children:',$this->getNodeChildren($node));
			//$content .= $this->doc->section('info:','Aggregator nodes do not have a log. Please select a test node to shwo the log');
			
		} else {
			
			//$content .= $this->doc->section( 'status:',  $this->getNodeStatus($node));
	 		//$content .= $this->doc->section( 'chart:',   $this->getNodeGraph($node, $range) );
	 		$content .= $this->doc->section('Log:', $this->getNodeLog($node));
			
		}
		
		return ($content);
		
	}
	
	function getNodeHeader($node){
		
		$nodeinfo = $node->getType().':'.$node->getTitle().'['.$node->getUid().']';
		if ($instance = $node->getInstance()){
			$instanceinfo = $instance->getType().':'.$instance->getTitle().'['.$instance->getUid().']';
		} else {
			$instanceinfo = '';
		}
		return $instanceinfo.' '.$nodeinfo;
	}
	
	function getNodeInfo($node){
		$local_time = localtime(time(), true);
		$local_hour = $local_time['tm_hour'];
		
		switch ( get_class($node) ){
			case "tx_caretaker_TestNode":
				
				$interval_info = '';
				$interval = $node->getInterval();
				if ( $interval < 60){
					$interval_info .= $interval.' Seconds';
				} else if ($interval < 60*60){
					$interval_info .= ($interval/60).' Minutes';
				} else if ($interval < 60*60*60){
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
				
				$info = '<table>'.
					'<tr><td>Title</td><td>'.$node->getTitle().'</td></tr>'.
					'<tr><td>Description</td><td>'.$node->getDescription().'</td></tr>'.
					'<tr><td>Interval</td><td>'.$interval_info.'</td></tr>'.
					'<tr><td>Hidden</td><td>'.$node->getHidden().'</td></tr>'.
				
					'</table>';
				break;
			default:
				$info = '<table>'.
					'<tr><td>Title</td><td>'.$node->getTitle().'</td></tr>'.
					'<tr><td>Description</td><td>'.$node->getDescription().'</td></tr>'.
					'<tr><td>Hidden</td><td>'.$node->getHidden().'</td></tr>'.
					'</table><br />'.
					'<span style="font-weight: bold;">Aggregator nodes do not have a log. Please select a test node to show the log.</span><br /><br />';
				break;
		}
		
		return $info;
	}
	
	function getNodeChildren($node){
		$children = $node->getChildren(true);
		$info = '';
		foreach ($children as $child){

			$row    = array(
				'uid'=>$child->getUid(), 
				'pid'=>0, 
				'title'=>$child->getTitle(), 
				'deleted'=>0, 
				'hidden'=>$child->getHidden(), 
				'starttime'=>0 ,
				'endtime'=>0, 
				'fe_group'=>0 
			);
			$table  = 'tx_caretaker_'.strToLower( $child->getType() );
			$title  = $child->getTitle();
			$icon   = t3lib_iconWorks::getIconImage($table,$row,$this->doc->backPath,'title="'.$title.'" align="top"').
			
			$params = false;
			
			switch ( $child->getType() ){
				case 'Instancegroup':
					$params = 'id=instancegroup_'.$child->getUid();
					break;
				case 'Instance':
					$params = 'id=instance_'.$child->getUid();
					break;
				case 'Testgroup':
					$instance = $child->getInstance();
					$params = 'id=instance_'.$instance->getUid().'_testgroup_'.$child->getUid();
					break;
				case 'Test':
					$instance = $child->getInstance();
					$params = 'id=instance_'.$instance->getUid().'_test_'.$child->getUid();
					break;		
			}
		
			$info .= '<a href="#" onclick="return jumpTo(\''.$params.'\',this,\''.$table.'_'.$uid.'\');">';
			$info .= $icon.' : '.$title."<br/>";
			$info .= '</a>';
			
		}
		return $info;
	}
	
	function getNodeStatus($node){
		$test_result = $node->getTestResult();
		switch( $test_result->getState() ){
			case 0:
				$color = 'green';
				break;
			case 1:
				$color .= 'orange';
				break;
			case 2:
				$color .= 'red';
				break;
			default:
				$color .= 'grey';
				break;			
		}
		
		
		switch ( get_class($node) ){
			case "tx_caretaker_TestNode": 
				$info = '<table>'.
					'<tr><td>State</td><td><span style="color:'.$color.';" >'.$test_result->getStateInfo().'</span></td></tr>'.
					'<tr><td>Value</td><td><span style="color:'.$color.';" >'.$test_result->getValue().'</span></td></tr>'.
					'<tr><td>lastRun</td><td><span style="color:'.$color.';" >'.strftime('%x %X',$test_result->getTstamp()).'</span></td></tr>'.
					'<tr><td>Comment</td><td><span style="color:'.$color.';" >'.$test_result->getMsg().'</span></td></tr>'.
					'</table>';
				break;
			default:
				$info = '<table>'.
					'<tr><td>State</td><td><span style="color:'.$color.';" >'.$test_result->getStateInfo().'</span></td></tr>'.
					'<tr><td>lastRun</td><td><span style="color:'.$color.';" >'.strftime('%x %X',$test_result->getTstamp()).'</span></td></tr>'.
					'<tr><td>Comment</td><td><span style="color:'.$color.';" >'.$test_result->getMsg().'</span></td></tr>'.
					'</table>';
				break; 
		}		
		return $info;
			
	}
	
	function getNodeIcon ($node){
		$uid    = $node->getUid();
		$title  = $node->getTitle();
		$hidden = $node->getHidden();
		$row    = array('uid'=>$uid, 'pid'=>0, 'title'=>$title, 'deleted'=>0, 'hidden'=>$hidden, 'starttime'=>0 ,'endtime'=>0, 'fe_group'=>0 );
		$table  = 'tx_caretaker_'.strToLower($node->getType());
		
		$result =	t3lib_iconWorks::getIconImage($table,$row,$this->doc->backPath,'title="foo" align="top"');
		return $result;
	}
	
	function getNodeActions($node) {
		$hidden = $node->getHidden();
		
		$BACK_URL   = urlencode(t3lib_div::getIndpEnv('TYPO3_REQUEST_URL')); 
		$PATH_TYPO3 = t3lib_div::getIndpEnv('TYPO3_SITE_URL').'typo3/';
		
		$actions = ''; 
		if (!$hidden) $actions .= '<a href="index.php?&id='.$_GET['id'].'&SET[function]='.$this->MOD_SETTINGS["function"].';&SET[action]=update" ><img src="../res/icons/arrow_refresh_small.png" title="refresh"/></a>';
		if (!$hidden) $actions .= '&nbsp;<a href="index.php?&id='.$_GET['id'].'&SET[function]='.$this->MOD_SETTINGS["function"].';&SET[action]=update_forced" ><img src="../res/icons/arrow_refresh.png" title="refresh forced"/></a>';
	                  $actions .= '&nbsp;<a href="#" onclick="window.location.href=\''.$PATH_TYPO3.'alt_doc.php?edit[tx_caretaker_'.strtolower($node->getType() ).']['.$node->getUid().']=edit&returnUrl='.$BACK_URL.'\';return false;" ><img src="../res/icons/pencil.png" title="edit"/></a>';
		if (!$hidden) $actions .= '&nbsp;<a href="#" onclick="window.location.href=\''.$PATH_TYPO3.'tce_db.php?&data[tx_caretaker_'.strtolower($node->getType() ).']['.$node->getUid().'][hidden]=1&redirect='.$BACK_URL.'\';return false;" ><img src="../res/icons/lightbulb_off.png" title="edit"/></a>';
		if ($hidden)  $actions .= '&nbsp;<a href="#" onclick="window.location.href=\''.$PATH_TYPO3.'tce_db.php?&data[tx_caretaker_'.strtolower($node->getType() ).']['.$node->getUid().'][hidden]=0&redirect='.$BACK_URL.'\';return false;" ><img src="../res/icons/lightbulb.png" title="edit"/></a>';
		return $actions;
	}
	
	function getNodeGraph($node, $range){
		
		require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_ResultRangeRenderer_pChart.php');

		$result_range = $node->getTestResultRange(time()-3600*$range , time() );	
		
		$filename = 'typo3temp/caretaker/charts/'.$this->id.'_'.$range.'.png';
		$renderer = tx_caretaker_ResultRangeRenderer_pChart::getInstance();
		$base_url = t3lib_div::getIndpEnv('TYPO3_SITE_URL');
		
		if (is_a($node, 'tx_caretaker_TestNode' ) ){
			$renderer->renderTestResultRange(PATH_site.$filename, $result_range , $node->getTitle(), $node->getValueDescription() );
			return '<img src="'.$base_url.$filename.'" />';
		} else  if (is_a( $node, 'tx_caretaker_AggregatorNode')){
			$renderer->renderAggregatorResultRange(PATH_site.$filename, $result_range , $node->getTitle());
			return '<img src="'.$base_url.$filename.'" />';
		}		
		return '<strong>Graph Error</strong>';
		
	}
	
	/**
	 * Creates the log for the node in the specified range
	 * @param $node The node for which the log should be displayed
	 * @return string The form and table for the node
	 */
	public function getNodeLog($node) {
		
		$result_range = $node->getTestResultRange(strtotime($this->timeFrom) , strtotime($this->timeTo), false);
		$result_range->reverse();
		
		$logForm = '<form action="" method="post">';
		$logForm .= '<label for="tx_caretaker_mod_log_timeFrom">From: </label>';
		$logForm .= '<input id="tx_caretaker_mod_log_timeFrom" type="text" name="tx_caretaker_mod_log[timeFrom]" value="'.$this->timeFrom.'" />';
		$logForm .= '<label for="tx_caretaker_mod_log_timeTo"> To: </label>';
		$logForm .= '<input id="tx_caretaker_mod_log_timeTo" type="text" name="tx_caretaker_mod_log[timeTo]" value="'.$this->timeTo.'" />';
		$logForm .= ' <input type="submit" value="Show log" />';
		$logForm .= '</form>';
		
		if($result_range->getLength() > 0) {
			
			// first result must be left out
			//$continue = true;
			
			$logTable = '<table><tbody>';
			$logTable .= '<tr><th>State</th><th>Date</th><th>Message</th></tr>';
			
			foreach($result_range as $result) {
				
				// first result must be left out
				/*if($continue) {
					
					$continue = false;
					continue;
				}*/
				
				if($result->getState() == TX_CARETAKER_STATE_OK) {
					
					$logTable .= '<tr><td style="background-color: #0d0; text-align: center; vertical-align: top;">'.$result->getStateInfo().'</td>';
					$logTable .= '<td style="background-color: #0d0; text-align: center; vertical-align: top; width: 90px;">'.date('Y-m-d, H:s',$result->getTimestamp()).'</td>';
					$logTable .= '<td style="background-color: #0d0; vertical-align: top;">'.nl2br($result->getMessage()).'</td>';
					$logTable .= '</tr>';
					
				} elseif($result->getState() == TX_CARETAKER_STATE_WARNING) {
					
					$logTable .= '<tr><td style="background-color: #dd0; text-align: center; vertical-align: top;">'.$result->getStateInfo().'</td>';
					$logTable .= '<td style="background-color: #dd0; text-align: center; vertical-align: top; width: 90px;">'.date('Y-m-d, H:s',$result->getTimestamp()).'</td>';
					$logTable .= '<td style="background-color: #dd0; vertical-align: top;">'.nl2br($result->getMessage()).'</td>';
					$logTable .= '</tr>';
					
				} elseif($result->getState() == TX_CARETAKER_STATE_ERROR) {
					
					$logTable .= '<tr><td style="background-color: #d00; text-align: center; vertical-align: top;">'.$result->getStateInfo().'</td>';
					$logTable .= '<td style="background-color: #d00; text-align: center; vertical-align: top; width: 90px;">'.date('Y-m-d, H:s',$result->getTimestamp()).'</td>';
					$logTable .= '<td style="background-color: #d00; vertical-align: top;">'.nl2br($result->getMessage()).'</td>';
					$logTable .= '</tr>';
					
				} else {
					
					$logTable .= '<tr><td style="text-align: center; vertical-align: top;">'.$result->getStateInfo().'</td>';
					$logTable .= '<td style="text-align: center; vertical-align: top; width: 90px;">'.date('Y-m-d, H:s',$result->getTimestamp()).'</td>';
					$logTable .= '<td style="vertical-align: top;">'.nl2br($result->getMessage()).'</td>';
					$logTable .= '</tr>';
				}
			}
			
			$logTable .= '</tbody></table>';
			
		} else {

			$logTable = '<p>There are no results in the specified range.</p>';
		}
		
		return $logForm.' '.$logTable;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/mod_log/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/mod_log/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_caretaker_mod_log');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>