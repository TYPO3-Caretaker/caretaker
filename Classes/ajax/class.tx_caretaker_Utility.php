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
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

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
 * Ajax methods which are used as ajaxID-methods by the
 * caretaker backend-module.
 *
 * @author     Thorben Kapp <thorben.kapp@kapp-hamburg.de>
 *
 * @package    TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_Utility {

	private $nodeTypeTables = array(
		'instance' => 'tx_caretaker_instance',
		'instancegroup' => 'tx_caretaker_instancegroup',
		'testgroup' => 'tx_caretaker_testgroup',
		'test' => 'tx_caretaker_test'
	);

	public function getModuleUrl() {
		$moduleUrl = '';
		$requestParameters = GeneralUtility::_GP('tx_caretaker');
		$table = $this->getTableByNodeType($requestParameters['type']);
		$node = $requestParameters['node'];

		switch ($requestParameters['mode']) {
			case 'add':
				$storagePid = $requestParameters['storagePid'];
				$defaultValues = array();
				switch ($requestParameters['type']) {
					case 'instance':
						if ($requestParameters['parent'] != 'root') {
							$defaultValues = array($table => array('instancegroup' => $requestParameters['parent']));
						}
						break;
					case 'instancegroup':
						if ($requestParameters['parent'] != 'root') {
							$defaultValues = array($table => array('parent_group' => $requestParameters['parent']));
						}
						break;
					case 'testgroup':
						if ($requestParameters['isInInstance'] == 1) {
							$defaultValues = array($table => array('instances' => $requestParameters['parent']));
						} else {
							$defaultValues = array($table => array('parent_group' => $requestParameters['parent']));
						}
						break;
					case 'test':
						if ($requestParameters['isInInstance'] == 1) {
							$defaultValues = array($table => array('instances' => $requestParameters['parent']));
						} else {
							$defaultValues = array($table => array('groups' => $requestParameters['parent']));
						}
						breaK;
				}
				if (VersionNumberUtility::convertVersionNumberToInteger(
						VersionNumberUtility::getNumericTypo3Version()
					) < VersionNumberUtility::convertVersionNumberToInteger('7.0.0')
				) {
					$moduleUrl = 'alt_doc.php?edit[' . $table . '][' . (int)$storagePid . ']=new&' . http_build_query(
							array('defVals' => $defaultValues)
						);
				} else {
					$moduleUrl = BackendUtility::getModuleUrl(
						'record_edit',
						array(
							'edit' => array($table => array($storagePid => 'new')),
							'defVals' => $defaultValues,
							'returnUrl' => $requestParameters['returnUrl']
						)
					);
				}
				break;
			case 'edit':
				if (VersionNumberUtility::convertVersionNumberToInteger(
						VersionNumberUtility::getNumericTypo3Version()
					) < VersionNumberUtility::convertVersionNumberToInteger('7.0.0')
				) {
					$moduleUrl = 'alt_doc.php?edit[' . $table . '][' . (int)$node . ']=edit';
				} else {
					$moduleUrl = BackendUtility::getModuleUrl(
						'record_edit',
						array('edit' => array($table => array($node => 'edit')))
					);
				}
				break;
			case 'hide':
				if (VersionNumberUtility::convertVersionNumberToInteger(
						VersionNumberUtility::getNumericTypo3Version()
					) < VersionNumberUtility::convertVersionNumberToInteger('7.0.0')
				) {
					$moduleUrl = 'tce_db.php?data['.$table.']['.$node.'][hidden]=1' . BackendUtility::getUrlToken('tceAction');
				} elseif (VersionNumberUtility::convertVersionNumberToInteger(
						VersionNumberUtility::getNumericTypo3Version()
					) < VersionNumberUtility::convertVersionNumberToInteger('7.6.0')) {
					$moduleUrl = BackendUtility::getAjaxUrl(
						'DataHandler::process',
						array('data' => array($table => array($node => array('hidden' => 1))))
					);
				} else {
					$moduleUrl = BackendUtility::getAjaxUrl(
						'record_process',
						array('data' => array($table => array($node => array('hidden' => 1))))
					);
				}
				break;
			case
			'unhide':
				if (VersionNumberUtility::convertVersionNumberToInteger(
						VersionNumberUtility::getNumericTypo3Version()
					) < VersionNumberUtility::convertVersionNumberToInteger('7.0.0')
				) {
					$moduleUrl = 'tce_db.php?data['.$table.']['.$node.'][hidden]=0' . BackendUtility::getUrlToken('tceAction');
				} elseif (VersionNumberUtility::convertVersionNumberToInteger(
						VersionNumberUtility::getNumericTypo3Version()
					) < VersionNumberUtility::convertVersionNumberToInteger('7.6.0')) {
					$moduleUrl = BackendUtility::getAjaxUrl(
						'DataHandler::process',
						array('data' => array($table => array($node => array('hidden' => 0))))
					);
				} else {
					$moduleUrl = BackendUtility::getAjaxUrl(
						'record_process',
						array('data' => array($table => array($node => array('hidden' => 0))))
					);
					break;
				}
		}
		echo $moduleUrl;
	}

	private function getTableByNodeType($nodeType) {
		return $this->nodeTypeTables[$nodeType];
	}
}
