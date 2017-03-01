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
abstract class tx_caretaker_pibase extends \TYPO3\CMS\Frontend\Plugin\AbstractPlugin
{
    protected $root_id = 'root';

    /**
     * The main method of the PlugIn
     *
     * @param  string $content The PlugIn content
     * @param  array $conf The PlugIn configuration
     * @return string The content that is displayed on the website
     */
    public function main($content, $conf)
    {
        $this->conf = $conf;
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();
        $this->pi_USER_INT_obj = 1;    // Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!

        if ($this->conf['root_id']) {
            $this->root_id = trim($this->conf['root_id']);
        }

        $content = $this->getContent();

        return $this->pi_wrapInBaseClass($content);
    }

    /**
     * @return mixed
     */
    abstract public function getContent();

    /**
     * @param tx_caretaker_AbstractNode $node
     * @return string
     */
    public function showNodeInfo($node)
    {
        // render first level Children
        if ($node instanceof tx_caretaker_AggregatorNode) {
            $template = $this->cObj->cObjGetSingle($this->conf['template'], $this->conf['template.']);

            $children = $node->getChildren();
            $child_template = $this->cObj->getSubpart($template, '###CARETAKER-CHILD###');
            $child_infos = '';
            foreach ($children as $child) {
                $data = $this->getNodeData($child);
                $lcObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
                $lcObj->start($data);
                $node_markers = array();
                if ($this->conf['childMarkers.']) {
                    foreach (array_keys($this->conf['childMarkers.']) as $key) {
                        if (substr($key, -1) != '.') {
                            $mark = $lcObj->cObjGetSingle($this->conf['childMarkers.'][$key], $this->conf['childMarkers.'][$key . '.']);
                            $node_markers['###' . $key . '###'] = $mark;
                        }
                    }
                    $child_infos .= $this->cObj->substituteMarkerArray($child_template, $node_markers);
                }
            }

            $template = $this->cObj->substituteSubpart($template, 'CARETAKER-CHILDREN', $child_infos);
        } else {
            $template = $this->cObj->cObjGetSingle($this->conf['templateChild'], $this->conf['templateChild.']);
        }

        // render Rootline
        $rootline_subpart = $this->cObj->getSubpart($template, '###ROOTLINE_ITEM###');
        $rootline_items = array();
        $rootline_node = $node;
        do {
            $data = $this->getNodeData($rootline_node);
            $lcObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
            $lcObj->start($data);
            $node_markers = array();
            if ($this->conf['rootlineMarkers.']) {
                foreach (array_keys($this->conf['rootlineMarkers.']) as $key) {
                    if (substr($key, -1) != '.') {
                        $mark = $lcObj->cObjGetSingle($this->conf['rootlineMarkers.'][$key], $this->conf['rootlineMarkers.'][$key . '.']);
                        $node_markers['###' . $key . '###'] = $mark;
                    }
                }
                $rootline_items[] = $this->cObj->substituteMarkerArray($rootline_subpart, $node_markers);
            }

            // Stop rootline if we reached the root node of the (sub)tree
            if ($rootline_node->getCaretakerNodeId() == $this->root_id) {
                break;
            }
        } while ($rootline_node = $rootline_node->getParent());

        $rootline_items = array_reverse($rootline_items);
        $template = $this->cObj->substituteSubpart($template, '###ROOTLINE###', implode('', $rootline_items));

        // render Node Infos
        $data = $this->getNodeData($node, true);
        $data['chart'] = $this->getNodeChart($node);
        $lcObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
        $lcObj->start($data);
        $node_markers = array();
        if ($this->conf['nodeMarkers.']) {
            foreach (array_keys($this->conf['nodeMarkers.']) as $key) {
                if (substr($key, -1) != '.') {
                    $mark = $lcObj->cObjGetSingle($this->conf['nodeMarkers.'][$key], $this->conf['nodeMarkers.'][$key . '.']);
                    $node_markers['###' . $key . '###'] = $mark;
                }
            }

            $template = $this->cObj->substituteMarkerArray($template, $node_markers);
        }

        return $template;
    }

    /**
     *
     * @param tx_caretaker_AbstractNode $node
     * @return array
     */
    public function getNodeData($node)
    {
        $data = array();

        // node data
        $data['uid'] = $node->getUid();
        $data['node_id'] = $node->getCaretakerNodeId();
        $data['node_type'] = $node->getType();
        $data['type'] = $node->getTypeDescription();
        $data['configuration'] = $node->getConfigurationInfo();
        $data['title'] = $node->getTitle();
        $data['description'] = $node->getDescription();

        // add state Infos
        $result = $node->getTestResult();
        $data['state'] = $result->getState();
        $data['state_info'] = $result->getStateInfo();
        $data['state_show'] = $result->getLocallizedStateInfo();
        $data['state_msg'] = $result->getLocallizedInfotext();
        $data['state_tstamp'] = $result->getTimestamp();

        if ($result instanceof tx_caretaker_TestResult) {
            $data['state_value'] = $result->getValue();
        }

        // instance data
        if (is_a($node, 'tx_caretaker_TestNode') || is_a($node, 'tx_caretaker_TestgroupNode')) {
            $data['instance'] = $node->getInstance()->getTitle();
        }

        $data['link_parameters'] = '&tx_caretaker_pi_singleview[id]=' . $node->getCaretakerNodeId();

        return $data;
    }

    /**
     * Get the chart for the node. Has to be implemented in subclasses
     *
     * @return string
     */
    public function getNodeChart()
    {
        return false;
    }
}
