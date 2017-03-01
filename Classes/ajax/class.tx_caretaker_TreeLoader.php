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
 * Ajax methods which are used as ajaxID-methods
 * by the caretaker-tree backend-module.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_TreeLoader
{

    /**
     * @param array $params
     * @param \TYPO3\CMS\Core\Http\AjaxRequestHandler $ajaxObj
     */
    public function ajaxLoadTree($params, &$ajaxObj)
    {
        $node_id = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('node');

        if ($node_id == 'root') {
            $node_repository = tx_caretaker_NodeRepository::getInstance();
            $node = $node_repository->getRootNode(true);
            $result = $this->nodeToArray($node, 2);
        } else {
            $node_repository = tx_caretaker_NodeRepository::getInstance();
            $node = $node_repository->id2node($node_id, 1);
            $result = $this->nodeToArray($node);
        }

        $ajaxObj->setContent($result['children']);
        $ajaxObj->setContentFormat('jsonbody');
    }

    /**
     * @param tx_caretaker_AbstractNode $node
     * @param int $depth
     * @return array
     */
    protected function nodeToArray($node, $depth = 1)
    {
        // show node and icon
        $result = [];
        $uid = $node->getUid();
        $title = $node->getTitle();
        $hidden = $node->getHidden();

        $id = $node->getCaretakerNodeId();

        $testResult = $node->getTestResult();
        $resultClass = 'caretaker-state-' . strtolower($testResult->getStateInfo());
        $typeClass = 'caretaker-type-' . strtolower($node->getType());

        $result['type'] = strtolower($node->getType());
        $result['id'] = $id;
        $result['uid'] = $uid;
        $result['disabled'] = $hidden;
        $result['text'] = $title ? $title : '[no title]';
        $result['cls'] = $resultClass . ' ' . $typeClass;
        $result['iconCls'] = 'icon-' . $typeClass . ($hidden ? '-hidden' : '');
        if (strtolower($node->getType()) == 'instance' && $node instanceof tx_caretaker_InstanceNode) {
            $result['url'] = $node->getUrl();
        } else {
            $result['url'] = false;
        }

        // show subitems of tx_caretaker_AggregatorNodes
        if ($node instanceof tx_caretaker_AggregatorNode) {
            $children = $node->getChildren(true);
            $result['leaf'] = (count($children) == 0) ? true : false;
            if ($depth > 0) {
                $result['children'] = [];
                foreach ($children as $child) {
                    $result['children'][] = $this->nodeToArray($child, $depth - 1);
                }
            }
        } else {
            $result['leaf'] = true;
        }

        return $result;
    }
}
