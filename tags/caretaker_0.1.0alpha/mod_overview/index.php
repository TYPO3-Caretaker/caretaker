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

unset($MCONF);
require('conf.php');
require_once($BACK_PATH . 'init.php');
require_once($BACK_PATH . 'template.php');

$GLOBALS['LANG']->includeLLFile("EXT:caretaker/mod_nav/locallang.xml");
$GLOBALS['BE_USER']->modAccess($MCONF, 1);

class tx_caretaker_mod_nav extends t3lib_SCbase {
	var $pageinfo;
	var $node_repository;
	var $instance_repository;
	var $node_id;
        
        /**
	 * @var t3lib_PageRenderer
	 */
        var $pageRenderer;
	
	/**
	 * Initializes the Module
	 * @return	void
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		parent::init();
		$this->node_id = $_GET['id'];
	}



	/**
	 * Main function of the module. Write the content to $this->content
	 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	 *
	 * @return	[type]		...
	 */
	function main()	{
		global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;
 
		$PATH_TYPO3 = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . 'typo3/';

		if ($BE_USER->user["admin"]) {

				// find node
			$node_repository = tx_caretaker_NodeRepository::getInstance();
			$node = $node_repository->id2node( $this->node_id , true);
			if (!$node) $node = $node_repository->getRootNode();

				// Draw the header.
			$this->doc = t3lib_div::makeInstance("template");
			$this->doc->backPath = $BACK_PATH;
			$this->pageRenderer = $this->doc->getPageRenderer();
                        
			// Include Ext JS
			$this->pageRenderer->loadExtJS();
			$this->pageRenderer->enableExtJSQuickTips();
			$this->pageRenderer->addJsFile('../res/js/tx.caretaker.js');

			$panels = array();
			foreach( $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['caretaker']['extJsBackendPanels'] as $extJsBackendPanel ){

					// register JS
				foreach($extJsBackendPanel['jsIncludes'] as $jsInclude){
					$filename = t3lib_div::getFileAbsFileName($jsInclude);
					$filename = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . str_replace( PATH_site, '', $filename  );
					$this->pageRenderer->addJsFile( $filename );
				}

					// register CSS
				foreach($extJsBackendPanel['cssIncludes'] as $cssInclude){
					$filename = t3lib_div::getFileAbsFileName($cssInclude);
					$filename = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . str_replace( PATH_site, '', $filename  );
					$this->pageRenderer->addCssFile( $filename );
				}

					// add ExtJs Panel
				$panels[ $extJsBackendPanel['id'] ] = $extJsBackendPanel['xtype'];
				
				
			}
			
			$this->pageRenderer->addJsFile('../res/js/tx.caretaker.NodeToolbar.js');

			// Enable debug mode for Ext JS
			$this->pageRenderer->enableExtJsDebug();

			// storage Pid
			$confArray = unserialize( $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
			$storagePid = (int)$confArray['storagePid'];
			
			//Add caretaker css
			$this->pageRenderer->addCssFile('../res/css/tx.caretaker.overview.css');

			$pluginItems = array();
			foreach ($panels as $id=>$xtype){
				$pluginItems[] = '{ id: "'.$id.'", xtype: "'.$xtype.'" , back_path: back_path , node_id: node_id }';
			}
			
			$this->pageRenderer->addJsInlineCode('Caretaker_Overview','
				Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
				Ext.namespace("tx","tx.caretaker");

				Ext.QuickTips.init();

				Ext.onReady( function() {

					var back_path   = "'.$this->doc->backPath.'";
					var back_url    = "'.urlencode(t3lib_div::getIndpEnv('TYPO3_REQUEST_URL')).'";
					var path_typo3  = "'.t3lib_div::getIndpEnv('TYPO3_SITE_URL').'typo3/";
					var	add_url     = "' . $PATH_TYPO3 . 'alt_doc.php?edit[###NODE_TYPE###][' . $storagePid . ']=new";
					var node_id     = "'.$node->getCaretakerNodeId().'";
					var node_type   = "'.strtolower($node->getType()).'";
					var node_hidden = "'.$node->getHidden().'";
					var node_uid    = "'.$node->getUid().'";
					var node_title  = "'.htmlspecialchars( $node->getTitle() ? $node->getTitle() : '[no title]' ).'( '.( $node->getTypeDescription() ? htmlspecialchars($node->getTypeDescription()) : $node->getType() ).' )" ;

					tx.caretaker.view = new Ext.Viewport({
						layout: "fit",
						items: {
								xtype    : "panel",
								id       : "node",
								autoScroll: true,
								title    : node_title,
								iconCls  : "icon-caretaker-type-" + node_type,
								tbar     : {
									xtype: "caretaker-nodetoolbar",
									back_path: back_path,
									path_typo3: path_typo3,
									back_url: back_url,
									add_url :add_url,
									node_id: node_id,
									node_type: node_type,
									node_uid: node_uid,
									node_hidden: node_hidden
								},
								items    : [
									'. implode( chr(10).',' , $pluginItems ). chr(10) . '
								]
						}
					 });

				});
			');
	
			$this->content .= $this->doc->startPage($LANG->getLL("title"));
			$this->doc->form = '';
		} else {
				// If no access or if not admin

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
		$this->content .= $this->doc->endPage();
		echo $this->content;
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