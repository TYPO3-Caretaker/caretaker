<?php
namespace Caretaker\Caretaker\Module;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use TYPO3\CMS\Backend\Module\BaseScriptClass;
use TYPO3\CMS\Backend\Template\DocumentTemplate;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Navigation extends BaseScriptClass
{
    public $pageinfo;

    public $node_repository;

    public $instance_repository;

    /**
     * @var PageRenderer
     */
    public $pageRenderer;

    public function __construct()
    {
        $this->getLanguageService()->includeLLFile('EXT:caretaker/mod_nav/locallang.xml');
    }

    public function mainAction(ServerRequestInterface $request, ResponseInterface $response)
    {
        $PATH_TYPO3 = GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . 'typo3/';

        if ($this->getBackendUser()->user['admin']) {
            // Draw the header.
            $this->doc = GeneralUtility::makeInstance(DocumentTemplate::class);
            $this->doc->backPath = $GLOBALS['$BACK_PATH'];
            $this->pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);

            // Include Ext JS
            $this->pageRenderer->loadExtJS(true, true);
            $this->pageRenderer->enableExtJsDebug();
            $this->pageRenderer->addJsFile(ExtensionManagementUtility::extRelPath('caretaker') . 'res/js/tx.caretaker.js', 'text/javascript', false, false);
            $this->pageRenderer->addJsFile(ExtensionManagementUtility::extRelPath('caretaker') . 'res/js/tx.caretaker.NodeTree.js', 'text/javascript', false, false);

            //Add caretaker css
            $this->pageRenderer->addCssFile(ExtensionManagementUtility::extRelPath('caretaker') . 'Resources/Public/Css/tx.caretaker.nodetree.css', 'stylesheet', 'all', '', false);

            // storage Pid
            $confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['caretaker']);
            $storagePid = (int)$confArray['storagePid'];

            $this->pageRenderer->addJsInlineCode(
                'Caretaker_Nodetree',
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
			'
            );

            $this->content .= $this->doc->startPage($this->getLanguageService()->getLL('title'));
            $this->doc->form = '';
        } else {
            // If no access or if not admin

            $this->doc = GeneralUtility::makeInstance('TYPO3\CMS\Backend\Template\MediumDocumentTemplate');
            $this->doc->backPath = $GLOBALS['$BACK_PATH'];

            $this->content .= $this->doc->startPage($this->getLanguageService()->getLL('title'));
            $this->content .= $this->doc->header($this->getLanguageService()->getLL('title'));
        }

        $this->content .= $this->doc->endPage();
        $response->getBody()->write($this->content);
        return $response;
    }
}
