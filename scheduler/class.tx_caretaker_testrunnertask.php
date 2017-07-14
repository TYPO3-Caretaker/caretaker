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
 * Sceduler Task to update the status of a given caretakerNodeId.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 */
class tx_caretaker_TestrunnerTask extends \TYPO3\CMS\Scheduler\Task\AbstractTask
{
    /**
     * @var string
     */
    protected $node_id;

    /**
     * @param string $id
     */
    public function setNodeId($id)
    {
        $this->node_id = $id;
    }

    /**
     * @return string
     */
    public function getNodeId()
    {
        return $this->node_id;
    }

    /**
     * @return bool
     */
    public function execute()
    {
        $node_repository = tx_caretaker_NodeRepository::getInstance();
        $node = $node_repository->id2node($this->node_id);

        if (!$node) {
            return false;
        }

        if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['lockingMode'] != 'disable') {
            $lockObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Locking\Locker', 'tx_caretaker_update_' . $node->getCaretakerNodeId(), $GLOBALS['TYPO3_CONF_VARS']['SYS']['lockingMode']);
            // no output during scheduler runs
            tx_caretaker_ServiceHelper::unregisterCaretakerNotificationService('CliNotificationService');

            if ($lockObj->acquire()) {
                $node->updateTestResult();
                $lockObj->release();
            } else {
                return true;
            }
        } else {
            $node->updateTestResult();
        }

        // send aggregated notifications
        $notificationServices = tx_caretaker_ServiceHelper::getAllCaretakerNotificationServices();
        /** @var tx_caretaker_AbstractNotificationService $notificationService */
        foreach ($notificationServices as $notificationService) {
            $notificationService->sendNotifications();
        }

        return true;
    }
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/caretaker/scheduler/class.tx_caretaker_testrunnertask.php']) {
    include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/caretaker/scheduler/class.tx_caretaker_testrunnertask.php']);
}
