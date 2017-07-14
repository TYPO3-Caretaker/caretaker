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
 * Class that adds the wizard icon.
 */
class tx_caretaker_pi_singleview_wizicon
{
    /**
     * @param $wizardItems
     * @return mixed
     */
    public function proc($wizardItems)
    {
        global $LANG;

        $LL = $this->includeLocalLang();

        $wizardItems['plugins_caretaker_pi_singleview'] = array(
            'icon' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('caretaker') . 'pi_singleview/ce_wiz.gif',
            'title' => $LANG->getLLL('pi_singleview_title', $LL),
            'description' => $LANG->getLLL('pi_singleview_plus_wiz_description', $LL),
            'params' => '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=caretaker_pi_singleview',
        );

        return $wizardItems;
    }

    /**
     * @return mixed
     */
    public function includeLocalLang()
    {
        global $LANG;

        $LOCAL_LANG = $LANG->includeLLFile('EXT:caretaker/locallang_db.xml', false);

        return $LOCAL_LANG;
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/pi_singleview/class.user_singleview_pi1_wizicon.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/caretaker/pi_singleview/class.user_singleview_pi1_wizicon.php']);
}
