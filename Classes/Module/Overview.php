<?php
namespace Caretaker\Caretaker\Module;

use TYPO3\CMS\Backend\Module\BaseScriptClass;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Overview extends BaseScriptClass
{
    public $pageinfo;

    public $node_repository;

    public $instance_repository;

    public $node_id;

    /**
     * @var \TYPO3\CMS\Core\Page\PageRenderer
     */
    public $pageRenderer;

    public function mainAction(
        \Psr\Http\Message\ServerRequestInterface $request,
        \Psr\Http\Message\ResponseInterface $response
    ) {
        $get = $request->getQueryParams();
        $this->node_id = $get['id'];

        global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;

        $PATH_TYPO3 = GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . 'typo3/';

        if ($BE_USER->user['admin']) {
            // find node
            $node_repository = \tx_caretaker_NodeRepository::getInstance();
            $node = $node_repository->id2node($this->node_id, true);
            if (!$node) {
                $node = $node_repository->getRootNode();
            }

            // Draw the header.
            $this->doc = GeneralUtility::makeInstance('TYPO3\CMS\Backend\Template\DocumentTemplate');
            $this->doc->backPath = $BACK_PATH;
            $this->pageRenderer = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);

            // Include Ext JS
            $this->pageRenderer->loadExtJS();
            $this->pageRenderer->addJsFile($BACK_PATH . ExtensionManagementUtility::extRelPath('caretaker') . 'res/js/tx.caretaker.js');

            $panels = array();
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['caretaker']['extJsBackendPanels'] as $extJsBackendPanel) {
                // register JS
                foreach ($extJsBackendPanel['jsIncludes'] as $jsInclude) {
                    $filename = $BACK_PATH . '../' . str_replace(Environment::getPublicPath() . '/', '', GeneralUtility::getFileAbsFileName($jsInclude));
                    $this->pageRenderer->addJsFile($filename);
                }

                // register CSS
                foreach ($extJsBackendPanel['cssIncludes'] as $cssInclude) {
                    $filename = $BACK_PATH . '../' . str_replace(Environment::getPublicPath() . '/', '', GeneralUtility::getFileAbsFileName($cssInclude));
                    $this->pageRenderer->addCssFile($filename);
                }

                // add ExtJs Panel
                $panels[$extJsBackendPanel['id']] = $extJsBackendPanel['xtype'];
            }

            $this->pageRenderer->addJsFile($BACK_PATH . ExtensionManagementUtility::extRelPath('caretaker') . 'res/js/tx.caretaker.NodeToolbar.js');

            // Enable debug mode for Ext JS
            $this->pageRenderer->enableExtJsDebug();

            // storage Pid
            $confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
            $storagePid = (int)$confArray['storagePid'];

            //Add caretaker css
            $this->pageRenderer->addCssFile($BACK_PATH . ExtensionManagementUtility::extRelPath('caretaker') . 'Resources/Public/Css/Overview.css');

            $pluginItems = array();
            foreach ($panels as $id => $xtype) {
                $pluginItems[] = '{ id: "' . $id . '", xtype: "' . $xtype . '" , back_path: back_path , node_id: node_id }';
            }

            $this->pageRenderer->addJsInlineCode('Caretaker_Overview', '
				Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
				Ext.namespace("tx","tx.caretaker");

				Ext.onReady( function() {

					var back_path   = "' . $this->doc->backPath . '";
					var back_url    = "' . urlencode(GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL')) . '";
					var path_typo3  = "' . GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . 'typo3/";
					var	add_url     = "' . $PATH_TYPO3 . 'alt_doc.php?edit[###NODE_TYPE###][' . $storagePid . ']=new";
					var node_id     = "' . $node->getCaretakerNodeId() . '";
					var node_type   = "' . strtolower($node->getType()) . '";
					var node_hidden = "' . $node->getHidden() . '";
					var node_uid    = "' . $node->getUid() . '";
					var node_title  = "' . htmlspecialchars($node->getTitle() ? $node->getTitle() : '[no title]') . '( ' . ($node->getTypeDescription() ? htmlspecialchars($node->getTypeDescription()) : $node->getType()) . ' )" ;
					var node_state  = "' . $node->getTestResult()->getState() . '" ;
					var node_state_info  = "' . $node->getTestResult()->getStateInfo() . '" ;

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
									node_hidden: node_hidden,
									node_state: node_state,
									node_state_info: node_state_info,
									getModuleUrlUrl: TYPO3.settings.ajaxUrls[\'tx_caretaker::getModuleUrl\'],
									storagePid: ' . $storagePid . '
								},
								items    : [
									{
										xtype    : "panel",
										padding  : "10",
										layout   : "fit",
										id       : "caretaker-panels",
										items    : [
											' . implode(chr(10) . ',', $pluginItems) . chr(10) . '
										]
									}
								],
						}
					 });
				});
			');

            $this->content .= $this->doc->startPage($LANG->getLL('title'));
            $this->doc->form = '';
        } else {
            // If no access or if not admin

            $this->doc = GeneralUtility::makeInstance('TYPO3\CMS\Backend\Template\MediumDocumentTemplate');
            $this->doc->backPath = $BACK_PATH;

            $this->content .= $this->doc->startPage($LANG->getLL('title'));
            $this->content .= $this->doc->header($LANG->getLL('title'));
        }
        $this->content .= $this->doc->endPage();
        $response->getBody()->write($this->content);
        return $response;
    }
}
