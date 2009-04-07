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
 * @author	 <>
 */



	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require ("conf.php");
require ($BACK_PATH."init.php");
require ($BACK_PATH."template.php");
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_InstancegroupRepository.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_InstanceRepository.php');

$LANG->includeLLFile("EXT:caretaker/mod_nav/locallang.xml");
require_once (PATH_t3lib."class.t3lib_scbase.php");
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]

class tx_caretaker_mod_nav extends t3lib_SCbase {
	var $pageinfo;
	var $instancegroup_repository;
	var $instance_repository;
	
	/**
	 * Initializes the Module
	 * @return	void
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		parent::init();

		/*
		if (t3lib_div::_GP("clear_all_cache"))	{
			$this->include_once[]=PATH_t3lib."class.t3lib_tcemain.php";
		}
		*/
		
		$this->instancegroup_repository = tx_caretaker_InstancegroupRepository::getInstance();
		$this->instance_repository = tx_caretaker_InstanceRepository::getInstance();
		
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
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

		if (($this->id && $access) || ($BE_USER->user["admin"] && !$this->id))	{

				// Draw the header.
			$this->doc = t3lib_div::makeInstance("template");
			$this->doc->backPath = $BACK_PATH;
			$this->doc->form='<form action="" method="POST">';

				// Setting JavaScript for menu.
			$this->doHighlight = 1;	
			$this->doc->JScode=$this->doc->wrapScriptTags(
			($this->currentSubScript?'top.currentSubScript=unescape("'.rawurlencode($this->currentSubScript).'");':'').'

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
			
			// Highlighting rows in the page tree:
			function hilight_row(frameSetModule,highLightID) { //
					// Remove old:
				theObj = document.getElementById(top.fsMod.navFrameHighlightedID[frameSetModule]);
				if (theObj)	{
					theObj.style.backgroundColor="";
				}

					// Set new:
				top.fsMod.navFrameHighlightedID[frameSetModule] = highLightID;
				theObj = document.getElementById(highLightID);
				if (theObj)	{
					theObj.style.backgroundColor="'.t3lib_div::modifyHTMLColorAll($this->doc->bgColor,-5).'";
				}
			}
			
			');

			$headerSection = $this->doc->getHeader("pages",$this->pageinfo,$this->pageinfo["_thePath"])."<br />".$LANG->sL("LLL:EXT:lang/locallang_core.xml:labels.path").": ".t3lib_div::fixed_lgd_pre($this->pageinfo["_thePath"],50);

			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header($LANG->getLL("title"));

			// Render content:
			$this->moduleContent();

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
	function moduleContent() {
		
		$root_instancegroups = $this->instancegroup_repository->getByParentGroupUid(0, false);
		
		foreach($root_instancegroups as $instancegroup){
			$this->content.= $this->show_node_recursive($instancegroup);
		}
		
		$root_instances = $this->instance_repository->getByInstancegroupUid(0, false);
		foreach($root_instances as $instance){
			$this->content.= $this->show_node_recursive($instance);
		}
		
	}
	
	function show_node_recursive($node, $level=0 ){
		
			// show node and icon
		$result = '';
		$uid    = $node->getUid();
		$title  = $node->getTitle();
		$row    = array('uid'=>$uid, 'pid'=>0, 'title'=>$title, 'deleted'=>0, 'hidden'=>0, 'starttime'=>0 ,'endtime'=>0, 'fe_group'=>0 );
		$table  = 'tx_caretaker_'.strToLower($node->getType());
		
		$params = false;
		switch (true){
			case is_a($node, 'tx_caretaker_Instancegroup'):
				$params = 'id=instancegroup_'.$uid;
				break;
			case is_a($node, 'tx_caretaker_Instance'):
				$params = 'id=instance_'.$uid;
				break;
			case is_a($node, 'tx_caretaker_Testgroup'):
				$instance = $node->getInstance();
				$params = 'id=instance_'.$instance->getUid().'_testgroup_'.$uid;
				break;
			case is_a($node, 'tx_caretaker_Test'):
				$instance = $node->getInstance();
				$params = 'id=instance_'.$instance->getUid().'_test_'.$uid;
				break;		
		}
		
			
		$result .= str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;',$level);
		$result .= '<a href="#" onclick="return jumpTo(\''.$params.'\',this,\''.$table.'_'.$uid.'\');">&nbsp;&nbsp;';
		
		
		$test_result = $node->getTestResult();
		switch( $test_result->getState() ){
			case 0:
				$result .= '<span style="color:green;">';
				break;
			case 1:
				$result .= '<span style="color:orange;">';
				break;
			case 2:
				$result .= '<span style="color:red;">';
				break;
			default:
				$result .= '<span style="color:grey;">';
				break;			
		}
		
		$result .=	t3lib_iconWorks::getIconImage($table,$row,$this->doc->backPath,'title="foo" align="top"').
				'&nbsp;'.htmlspecialchars($row['title']);
		$result .= '</span>';		
		$result .= '</a></li>';
		$result .= '<br/>';		
		
			// show subitems of tx_caretaker_AggregatorNodes
		if (is_a($node, 'tx_caretaker_AggregatorNode') ){
			$children = $node->getChildren();
			if (count($children)>0){
				foreach($children as $child){
					$result.= $this->show_node_recursive($child, $level + 1) ;
				}
			}
		}
		
		return $result;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/mod_nav/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/mod_nav/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_caretaker_mod_nav');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>