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
class tx_caretaker_pi_graphreport extends tx_caretaker_pibase
{
    public $prefixId = 'tx_caretaker_pi_graphreport';        // Same as class name

    public $scriptRelPath = 'pi_graphreport/class.tx_caretaker_pi_graphreport.php';    // Path to this script relative to the extension dir.

    public $extKey = 'caretaker';    // The extension key.

    const PATH_CHARTS = 'typo3temp/caretaker/charts/report';

    public function main($content, $conf)
    {
        $this->pi_initPIflexForm();

        $this->initDirectories();

        return parent::main($content, $conf);
    }

    /**
     * @return string
     */
    public function getContent()
    {
        $template = $this->cObj->cObjGetSingle($this->conf['template'], $this->conf['template.']);

        // render Node Infos
        $data = $this->getData();
        $lcObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
        $lcObj->start($data);
        $node_markers = array();
        if ($this->conf['markers.']) {
            foreach (array_keys($this->conf['markers.']) as $key) {
                if (substr($key, -1) != '.') {
                    $mark = $lcObj->cObjGetSingle($this->conf['markers.'][$key], $this->conf['markers.'][$key . '.']);
                    $node_markers['###' . $key . '###'] = $mark;
                }
            }
            $template = $this->cObj->substituteMarkerArray($template, $node_markers);
        }

        return $template;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $data = $this->cObj->data;

        $range = $this->getTimeRange();
        $nodes = $this->getNodes();

        $titles = array();

        if (count($nodes) > 0) {
            $result_ranges = array();
            $id = '';
            $lastTitle = '';
            foreach ($nodes as $node) {
                if ($node instanceof tx_caretaker_TestNode) {
                    $result_ranges[] = $node->getTestResultRange(time() - (3600 * $range), time());
                    $titles[] = $node->getInstance()->getTitle() . ' - ' . $node->getTitle();
                    $id .= $node->getCaretakerNodeId();
                    $lastTitle = $node->getTitle();
                }
            }

            if (count($result_ranges) > 0) {
                $filename = 'typo3temp/caretaker/charts/report_' . $id . '_' . $range . '.png';
                $base = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SITE_URL');

                $MultipleTestResultRangeChartRenderer = new tx_caretaker_MultipleTestResultRangeChartRenderer();
                $MultipleTestResultRangeChartRenderer->setTitle($lastTitle);

                foreach ($result_ranges as $key => $range) {
                    $MultipleTestResultRangeChartRenderer->addTestResultrange($range, $titles[$key]);
                }

                $result = $MultipleTestResultRangeChartRenderer->getChartImageTag($filename, $base);
                $data['chart'] = $result;
            } else {
                $data['chart'] = 'please select one or more test-nodes';
            }
        } else {
            $data['chart'] = 'no node ids found';
        }

        return $data;
    }

    /**
     * @return int
     */
    public function getTimeRange()
    {
        $range = 24;

        $ts_range = (int)$this->conf['defaultRange'];
        if ($ts_range) {
            $range = $ts_range;
        }

        $ff_range = (int)$this->pi_getFFValue($this->cObj->data['pi_flexform'], 'time_range');
        if ($ff_range) {
            $range = $ff_range;
        }

        $pivar_range = (int)$this->piVars['range'];
        if ($pivar_range) {
            $range = $pivar_range;
        }

        return $range;
    }

    public function getNodes()
    {
        $node_ids = $this->pi_getFFValue($this->cObj->data['pi_flexform'], 'node_ids');
        // Node ids not specified? Try TypoScript instead
        if (!$node_ids && $this->conf['node_ids']) {
            $node_ids = $this->conf['node_ids'];
        }

        $nodes = array();
        $ids = explode(chr(10), $node_ids);
        $node_repository = tx_caretaker_NodeRepository::getInstance();

        foreach ($ids as $id) {
            $node = $node_repository->id2node($id);
            if (!$node) {
                continue;
            }

            if ($this->root_id !== 'root') {
                // Check if node is in the specified subtree
                $parent_node = $node;
                do {
                    // One parent of node should be the subtree root
                    if ($parent_node->getCaretakerNodeId() == $this->root_id) {
                        $nodes[] = $node;
                    }
                } while ($parent_node = $parent_node->getParent());
            } else {
                $nodes[] = $node;
            }
        }

        return $nodes;
    }


    private function initDirectories()
    {
        if (!is_dir(PATH_site . self::PATH_CHARTS)) {
            if (!mkdir(PATH_site . self::PATH_CHARTS, 0770, true)) {
                throw new \TYPO3\CMS\Core\Cache\Exception('can\'t create path "' . PATH_site . self::PATH_CHARTS . '"', 1465993775);
            }
        }
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/pi_graphreport/class.tx_caretaker_pi_graphreport.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/pi_graphreport/class.tx_caretaker_pi_graphreport.php']);
}
