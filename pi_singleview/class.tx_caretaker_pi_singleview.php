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
 * Plugin 'Overview' for the 'user_overview' extension.
 */
class tx_caretaker_pi_singleview extends tx_caretaker_pibase
{
    public $prefixId = 'tx_caretaker_pi_singleview';        // Same as class name

    public $scriptRelPath = 'pi_singleview/class.tx_caretaker_pi_singleview.php';    // Path to this script relative to the extension dir.

    public $extKey = 'caretaker';    // The extension key.

    const PATH_CHARTS = 'typo3temp/caretaker/charts';


    /**
     * @return string
     */
    public function getContent()
    {
        $node = $this->getNode();
        if ($node) {
            $content = $this->showNodeInfo($node);
        } else {
            $content = 'no node found';
        }

        return $content;
    }

    /**
     * @return tx_caretaker_AbstractNode
     */
    public function getNode()
    {
        $id = $this->piVars['id'];
        $node_repository = tx_caretaker_NodeRepository::getInstance();

        if ($id) {
            $node = $node_repository->id2node($id);
        } else {
            $this->pi_initPIflexForm();
            $node_id = $this->pi_getFFValue($this->cObj->data['pi_flexform'], 'node_id');
            // Node id not specified? Try TypoScript instead
            if (!$node_id && $this->conf['node_id']) {
                $node_id = $this->conf['node_id'];
            }

            $node = $node_repository->id2node($node_id);
        }

        if ($this->root_id !== 'root') {
            // Check if node is in the specified subtree
            $parent_node = $node;
            do {
                // One parent of node should be the subtree root
                if ($parent_node->getCaretakerNodeId() == $this->root_id) {
                    return $node;
                }
            } while ($parent_node = $parent_node->getParent());

            return false;
        }
        return $node;
    }

    /**
     * @param tx_caretaker_AbstractNode $node
     * @return array
     */
    public function getNodeData($node)
    {
        $data = parent::getNodeData($node);
        $range = 24;
        if ($this->piVars['range']) {
            $range = (int)$this->piVars['range'];
        }
        $data['range'] = $range / 24;

        return $data;
    }

    /**
     * @param tx_caretaker_AbstractNode $node
     * @return bool|string
     */
    public function getNodeChart($node)
    {
        $chart = false;

        $range = 24;
        if ($this->piVars['range']) {
            $range = (int)$this->piVars['range'];
        }

        $id = $node->getCaretakerNodeID();
        $result_range = $node->getTestResultRange(time() - 3600 * $range, time());
        if (!is_dir(PATH_site . self::PATH_CHARTS)) {
            if (!mkdir(PATH_site . self::PATH_CHARTS, 0770, true)) {
                throw new \TYPO3\CMS\Core\Cache\Exception('can\'t create path "' . PATH_site . self::PATH_CHARTS . '"', 1465993775);
            }
        }
        $filename = 'typo3temp/caretaker/charts/' . $id . '_' . $range . '.png';
        $base = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SITE_URL');

        if (is_a($node, 'tx_caretaker_TestNode')) {
            $TestResultRangeChartRenderer = new tx_caretaker_TestResultRangeChartRenderer();
            $TestResultRangeChartRenderer->setTitle($node->getTitle());
            $TestResultRangeChartRenderer->setTestResultRange($result_range);
            $result = $TestResultRangeChartRenderer->getChartImageTag($filename, $base);

            if ($result) {
                $chart = $result;
            } else {
                $chart = 'Graph Error';
            }
        } elseif (is_a($node, 'tx_caretaker_AggregatorNode')) {
            $TestResultRangeChartRenderer = new tx_caretaker_AggregatorResultRangeChartRenderer();
            $TestResultRangeChartRenderer->setTitle($node->getTitle());
            $TestResultRangeChartRenderer->setAggregatorResultRange($result_range);
            $result = $TestResultRangeChartRenderer->getChartImageTag($filename, $base);

            if ($result) {
                $chart = $result;
            } else {
                $chart = 'Graph Error';
            }
        }

        return $chart;
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/pi_singleview/class.tx_caretaker_pi_singleview.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/pi_singleview/class.tx_caretaker_pi_singleview.php']);
}
