<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2009-2011 by n@work GmbH and networkteam GmbH
 *
 * All rights reserved
 *
 * This script is part of the Caretaker project. The Caretaker project
 * is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * This is a file of the caretaker project.
 * http://forge.typo3.org/projects/show/extension-caretaker
 *
 * Project sponsored by:
 * n@work GmbH - http://www.work.de
 * networkteam GmbH - http://www.networkteam.com/
 *
 * $Id$
 */

/**
 * Module 'Caretaker' for the 'caretaker' extension.
 */
class tx_caretaker_mod_nav extends \TYPO3\CMS\Backend\Module\BaseScriptClass
{
    public $pageinfo;

    public $node_repository;

    public $instance_repository;

    /**
     * @var \TYPO3\CMS\Core\Page\PageRenderer
     */
    public $pageRenderer;

    public function __construct()
    {
        $GLOBALS['LANG']->includeLLFile('EXT:caretaker/mod_nav/locallang.xml');
    }

    /**
     * Initializes the Module
     *
     * @return    void
     */
    public function init()
    {
        global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;

        parent::init();
    }

    /**
     * Main function of the module. Write the content to $this->content
     * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
     */
    public function main()
    {
        global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;

        $PATH_TYPO3 = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . 'typo3/';

        if ($BE_USER->user['admin']) {
            // Draw the header.
            $this->doc = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Backend\\Template\\DocumentTemplate');
            $this->doc->backPath = $BACK_PATH;

            $this->pageRenderer = $this->doc->getPageRenderer();

            // Include Ext JS
            $this->pageRenderer->loadExtJS(true, true);
            $this->pageRenderer->enableExtJsDebug();
            $this->pageRenderer->addJsFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('caretaker') . 'res/js/tx.caretaker.js', 'text/javascript', false, false);
            $this->pageRenderer->addJsFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('caretaker') . 'res/js/tx.caretaker.NodeTree.js', 'text/javascript', false, false);

            //Add caretaker css
            $this->pageRenderer->addCssFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('caretaker') . 'Resources/Public/Css/Nodetree.css', 'stylesheet', 'all', '', false);

            // storage Pid
            $confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
            $storagePid = (int)$confArray['storagePid'];

            $this->pageRenderer->addJsInlineCode('Caretaker_Nodetree',
                '
			Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
			Ext.ns("tx.caretaker");
			Ext.onReady(function() {
				tx.caretaker.view = new Ext.Viewport({
					layout: "fit",
					items: {
						id: "cartaker-tree",
						xtype: "caretaker-nodetree",
						autoScroll: true,
						dataUrl: TYPO3.settings.ajaxUrls[\'tx_caretaker::treeloader\'],
						getModuleUrlUrl: TYPO3.settings.ajaxUrls[\'tx_caretaker::getModuleUrl\'],
						storagePid: ' . $storagePid . ',
						addUrl: "' . $PATH_TYPO3 . 'alt_doc.php?edit[###NODE_TYPE###][' . $storagePid . ']=new"
					}
				});

				tx_caretaker_updateTreeById = function( id ){
					tx_caretaker_tree = Ext.getCmp("cartaker-tree");
					tx_caretaker_tree.reloadTreePartial( id );
				}
			});
			');

            $this->content .= $this->doc->startPage($LANG->getLL('title'));
            $this->doc->form = '';
        } else {
            // If no access or if not admin

            $this->doc = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Backend\Template\MediumDocumentTemplate');
            $this->doc->backPath = $BACK_PATH;

            $this->content .= $this->doc->startPage($LANG->getLL('title'));
            $this->content .= $this->doc->header($LANG->getLL('title'));
            $this->content .= $this->doc->spacer(5);
            $this->content .= $this->doc->spacer(10);
        }
    }

    /**
     * Prints out the module HTML
     *
     * @return    void
     */
    public function printContent()
    {
        $this->content .= $this->doc->endPage();
        echo $this->content;
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/mod_nav/index.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/mod_nav/index.php']);
}

// Make instance:
$SOBE = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_caretaker_mod_nav');
$SOBE->init();

// Include files?
if ($SOBE->include_once) {
    foreach ($SOBE->include_once as $INC_FILE) {
        include_once($INC_FILE);
    }
}

$SOBE->main();
$SOBE->printContent();
