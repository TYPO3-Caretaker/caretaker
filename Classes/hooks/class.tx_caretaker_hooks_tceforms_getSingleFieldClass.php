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
 * Tceforms-hooks for per instance test-configuration overriding.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 */
class tx_caretaker_hooks_tceforms_getSingleFieldClass
{
    /**
     * @param string $table
     * @param string $field
     * @param array $row
     * @param array $PA
     */
    public function getSingleField_beforeRender($table, $field, &$row, &$PA)
    {
        global $TCA;

        if ($table == 'tx_caretaker_instance' && $field === 'testconfigurations') {
            switch ($PA['fieldConf']['config']['tag']) {
                case 'testconfigurations.test_service':
                    break;

                case 'testconfigurations.test_conf':
                    $test = $this->getFFValue($table, $row, $field, str_replace('test_conf', 'test_service', $PA['itemFormElName']));

                    // get related test configuration
                    $testrow = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
                        'uid,test_service,test_conf',
                        'tx_caretaker_test',
                        'uid=' . intval($test) . \TYPO3\CMS\Backend\Utility\BackendUtility::deleteClause('tx_caretaker_test')
                    );

                    $row['test_service'] = $testrow[0]['test_service'];
                    if ($PA['itemFormElValue'] == null) {
                        $PA['itemFormElValue'] = $testrow[0]['test_conf'];
                    }
                    if (is_array($PA['itemFormElValue'])) {
                        $PA['itemFormElValue'] = \TYPO3\CMS\Core\Utility\GeneralUtility::array2xml($PA['itemFormElValue']);
                    }

                    if (!is_array($PA['fieldConf']['config']['ds'])) {
                        $PA['fieldConf']['config']['ds'] = $TCA['tx_caretaker_test']['columns']['test_conf']['config']['ds'];
                    }
                    break;
            }
        }
    }

    /**
     * @param string $table
     * @param array $row
     * @param string $field
     * @param string $itemFormElName
     * @return mixed
     */
    protected function getFFValue($table, $row, $field, $itemFormElName)
    {
        $path = str_replace('data[' . $table . '][' . $row['uid'] . '][' . $field . '][', '', $itemFormElName);
        $path = rtrim($path, ']');
        $path = explode('][', $path);
        $fftools = new \TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools();
        $val = $fftools->getArrayValueByPath($path, \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($row[$field]));

        return $val;
    }
}
