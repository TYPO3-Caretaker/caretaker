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
$LANG->includeLLFile("EXT:caretaker/mod_overview/locallang.xml");
require_once (PATH_t3lib."class.t3lib_scbase.php");
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_InstancegroupRepository.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_InstanceRepository.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_TestgroupRepository.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_TestRepository.php');

$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]

class tx_caretaker_mod_overview extends t3lib_SCbase {

	var $info;
	
	/**
	 * Initializes the Module
	 * @return	void
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		parent::init();

		$this->id = $_GET['id'];
		$this->info = $this->extractID($_GET['id']);
		
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
				"0.25" => '6h',
				"0.5" => '12h',
				"1" => $LANG->getLL("today"),
				"3" => $LANG->getLL("days"),
				"14" => $LANG->getLL("weeks"),
				"90" => $LANG->getLL("months"),
				"365" => $LANG->getLL("year"),
		
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
			$this->doc = t3lib_div::makeInstance("mediumDoc");
			$this->doc->backPath = $BACK_PATH;
			$this->doc->form='<form action="" method="POST">';

				// JavaScript
			$this->doc->JScode = '
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL)	{
						document.location = URL;
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
			$this->content.=$this->doc->section("",$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,"SET[function]",$this->MOD_SETTINGS["function"],$this->MOD_MENU["function"])));
			$this->content.=$this->doc->divider(5);


			// Render content:
			$this->moduleContent();


			// ShortCut
			if ($BE_USER->mayMakeShortcut())	{
				$this->content.=$this->doc->spacer(20).$this->doc->section("",$this->doc->makeShortcutIcon("id",implode(",",array_keys($this->MOD_MENU)),$this->MCONF["name"]));
			}

			$this->content.=$this->doc->spacer(10);
		} else {
				// If no access or if ID == zero

			$this->doc = t3lib_div::makeInstance("mediumDoc");
			$this->doc->backPath = $BACK_PATH;

			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}
	}

	function extractID($id_string){
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
		return ($info); 
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
							
		$num_days = (float)$this->MOD_SETTINGS["function"]; 
		if (!$num_days) $num_days = 3;
		$this->content.= $this->showInfo($this->info, $num_days) ;
		
	}
	
	function showInfo($info, $num_days){

		$node = $this->getNode($info);
		if ($node){
			return ($this->showNodeInfo($node, $num_days));
		} else {
			return $this->doc->section( 'Error:','please select a node');
		}
	}
	
	function getNode($info){
		if ($info['instancegroup']>0){
			$instancegroup_repoistory    = tx_caretaker_InstancegroupRepository::getInstance();
			$instancegroup = $instancegroup_repoistory->getByUid($info['instancegroup'], false);
			return $instancegroup;		
		} else if ($info['instance']>0){
			$instance_repoistory    = tx_caretaker_InstanceRepository::getInstance();
			$instance = $instance_repoistory->getByUid($info['instance'], false);
			if ($info['testgroup']>0){
    			$group_repoistory    = tx_caretaker_TestgroupRepository::getInstance();
				$group = $group_repoistory->getByUid($info['testgroup'], $instance);
				return $group;		
    		} else if ($info['test']>0) {
    			$test_repoistory    = tx_caretaker_TestRepository::getInstance();
				$test = $test_repoistory->getByUid($info['test'], $instance);
				return $test;		
    		} else {
				return $instance;		
			}
		} else {
			return false;
		}
	}
	
	function showNodeInfo($node, $num_days){

		$content = '';
		$nodeinfo = $node->getType().':'.$node->getUid();
		if ($instance = $node->getInstance()){
			$nodeinfo .= ' :: '.$instance->getType().':'.$instance->getUid();
		}
		$content .= $this->doc->header($nodeinfo );
		
		$test_result = $node->getTestResult();
		$content .= $this->doc->section( 'current result:','<table>'.
			'<tr><td>State</td><td>'.$test_result->getStateInfo().'</td></tr>'.
			'<tr><td>Value</td><td>'.$test_result->getValue().'</td></tr>'.
			'<tr><td>lastRun</td><td>'.strftime('%x %X',$test_result->getTstamp()).'</td></tr>'.
			'<tr><td>Comment</td><td>'.$test_result->getComment().'</td></tr>'.
			'</table>'
		 );
		 
			// show graph
		if ($num_days){
			
			require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_TestResultRangeRenderer_pChart.php');
			
			$result_range = $node->getTestResultRange(time()-86400*$num_days , time());	
			$filename = 'typo3temp/caretaker/charts/'.$this->id.'_'.$num_days.'.png';
			$renderer = tx_caretaker_TestResultRangeRenderer_pChart::getInstance();
			$renderer->render($result_range, PATH_site.$filename);

			$base = t3lib_div::getIndpEnv('TYPO3_SITE_URL');
			$content .= $this->doc->section( 'chart:','<img src="'.$base.$filename.'" />');
			
		}
		return ($content);
		
	}
	
	/*
    
    function updateInstance( $instanceID, $groupID, $testID, $force = false ){
    	
		require_once ('class.tx_caretaker_InstanceRepository.php');
		
      	$instance_repoistory    = tx_caretaker_InstanceRepository::getInstance();
		$instance = $instance_repoistory->getByUid($instanceID, $this);
		$instance->setLogger($this);
		
		if ($instance) {

    		if ($groupID){
    			$group_repoistory    = tx_caretaker_GroupRepository::getInstance();
				$group = $group_repoistory->getByUid($groupID, $instance);
				$res = $group->updateState($force);
    		} else if ($testID) {
    			$test_repoistory    = tx_caretaker_TestRepository::getInstance();
				$test = $test_repoistory->getByUid($testID, $instance);
				$res = $test->updateState($force);
    		} else {
				$res = $instance->updateState($force);
			}
    	} else {
			$this->log('instance '.$instanceID.' not found'.chr(10));
		}
    }
    
    */
    
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/mod_overview/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/mod_overview/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_caretaker_mod_overview');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>