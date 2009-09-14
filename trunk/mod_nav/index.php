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
require_once (PATH_t3lib."class.t3lib_scbase.php");
$GLOBALS['BE_USER']->modAccess($MCONF, 1);

require_once (t3lib_extMgm::extPath('caretaker') . '/classes/repositories/class.tx_caretaker_NodeRepository.php');
require_once (t3lib_extMgm::extPath('caretaker') . '/classes/class.tx_caretaker_Helper.php');

class tx_caretaker_mod_nav extends t3lib_SCbase {
	var $pageinfo;
	var $node_repository;
	var $instance_repository;
	
	/**
	 * Initializes the Module
	 * @return	void
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		parent::init();				
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
				// Draw the header.
			$this->doc = t3lib_div::makeInstance("template");
			$this->doc->backPath = $BACK_PATH;

				// Include Ext JS
			$this->jsFiles = array(
				'/contrib/extjs/adapter/ext/ext-base.js',
				'/contrib/extjs/ext-all-debug.js',
				'../res/js/tx.caretaker.js',
				'../res/js/tx.caretaker.NodeTree.js'
			);
			$this->cssFiles = array(
				'extJS' => '/contrib/extjs/resources/css/ext-all.css',
				'extJS-gray' => '/contrib/extjs/resources/css/xtheme-gray.css',
				'caretaker-nodetree' => '../res/css/tx.caretaker.nodetree.css'
			);
			foreach($this->jsFiles as $jsFile) {
				$this->doc->JScode .= '
				<script type="text/javascript" src="' . (strpos($jsFile, '/') === 0 ? $this->doc->backPath . substr($jsFile, 1) : $jsFile) . '"></script>';
			}
			foreach($this->cssFiles as $cssFileName => $cssFile) {
				$this->doc->JScode .= '
				<link rel="stylesheet" type="text/css" href="' . (strpos($cssFile, '/') === 0 ? $this->doc->backPath . substr($cssFile, 1) : $cssFile) . '" />
				';
			}			
			
			$this->doc->JScode .= $this->doc->wrapScriptTags(
			'
			Ext.BLANK_IMAGE_URL = "' . $this->doc->backPath . 'contrib/extjs/resources/images/default/s.gif";
			Ext.QuickTips.init();
			Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
			Ext.onReady(function() {
				tx.caretaker.view = new Ext.Viewport({
					layout: "fit",
					items: {
						id: "cartaker-tree",
						xtype: "caretaker-nodetree",
	    				autoScroll: true,
						dataUrl: "' . $this->doc->backPath . 'ajax.php?ajaxID=tx_caretaker::treeloader",
						editUrl: "' . $PATH_TYPO3 . 'alt_doc.php?edit[tx_caretaker_###NODE_TYPE###][###NODE_UID###]=edit",
						hideUrl: "' . $PATH_TYPO3 . 'tce_db.php?&data[tx_caretaker_###NODE_TYPE###][###NODE_UID###][hidden]=1",
						unhideUrl: "' . $PATH_TYPO3 . 'tce_db.php?&data[tx_caretaker_###NODE_TYPE###][###NODE_UID###][hidden]=0"
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