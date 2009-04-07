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
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_Helper.php');

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
				"0.5" =>$LANG->getLL("day"),
				"1"   => $LANG->getLL("today"),
				"3"   => $LANG->getLL("days"),
				"14"  => $LANG->getLL("weeks"),
				"90"  => $LANG->getLL("months"),
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

		$node = tx_caretaker_Helper::getNode($this->info['instancegroup'], $this->info['instance'], $this->info['testgroup'], $this->info['test']);

		if ($node){
			if ( isset ($_GET['SET']['action']) ){
				if ($_GET['SET']['action'] == 'update'){
					$node->updateTestResult();	
				}
				if ($_GET['SET']['action'] == 'update_forced'){
					$node->updateTestResult(true);	
				}
			}
			$this->content.= ($this->showNodeInfo($node, $num_days));
		} else {
			$this->content.= $this->doc->section( 'Error:','please select a node');
		}
	}
		
	function showNodeInfo($node, $num_days){

		$content = '';
		$nodeinfo = $node->getType().':'.$node->getTitle().'['.$node->getUid().']';
		if ($instance = $node->getInstance()){
			$instanceinfo = $instance->getType().':'.$instance->getTitle().'['.$instance->getUid().']';
		} else {
			$instanceinfo = '';
		}
		$content .= $this->doc->header($instanceinfo.' '.$nodeinfo );
		
		$test_result = $node->getTestResult();
		$content .= $this->doc->section( 'current result:','<table>'.
			'<tr><td>State</td><td>'.$test_result->getStateInfo().'</td></tr>'.
			'<tr><td>Value</td><td>'.$test_result->getValue().'</td></tr>'.
			'<tr><td>lastRun</td><td>'.strftime('%x %X',$test_result->getTstamp()).'</td></tr>'.
			'<tr><td>Comment</td><td>'.$test_result->getMsg().'</td></tr>'.
			'</table>'
		 );
		
		$actions = ''; 
		$actions .= '<a href="index.php?&id='.$_GET['id'].'&SET[function]='.$this->MOD_SETTINGS["function"].';&SET[action]=update" >update</a>';
		$actions .= '&nbsp;<a href="index.php?&id='.$_GET['id'].'&SET[function]='.$this->MOD_SETTINGS["function"].';&SET[action]=update_forced" >update [force refresh]</a>';
		
		$content .= $this->doc->section( 'action:', $actions);
			// show graph
		if ($num_days){
			$content .= $this->doc->section( 'chart:',$this->showNodeGraph($node, $num_days) );
		}
		return ($content);
		
	}
	
	function showNodeGraph($node, $num_days){
		require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_TestResultRangeRenderer_pChart.php');

			$dist = $num_days*100;
			$result_range = $node->getTestResultRange(time()-86400*$num_days , time(), $dist );	
		
			$filename = 'typo3temp/caretaker/charts/'.$this->id.'_'.$num_days.'.png';
			$renderer = tx_caretaker_TestResultRangeRenderer_pChart::getInstance();
			$result   = $renderer->render($result_range, PATH_site.$filename);
			$base = t3lib_div::getIndpEnv('TYPO3_SITE_URL');
			
			if ($result){
				return '<img src="'.$base.$filename.'" />';
			} else {
				return '<strong>Graph Error</strong>';
			}
			
	}
    
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