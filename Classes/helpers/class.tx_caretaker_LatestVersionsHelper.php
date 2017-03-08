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
 * Helper to provides the functionality to fetch the latest TYPO3 Versions from SVN tags.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 * @author Tomas Norre Mikkelsen <tomasnorre@gmail.com>
 *
 */
class tx_caretaker_LatestVersionsHelper
{
    /**
     * @var string JSON release feed
     */
    protected static $releaseJsonFeed = 'https://get.typo3.org/json';

    /**
     * @return bool
     */
    public static function updateLatestTypo3VersionRegistry()
    {
        $releases = json_decode(self::curlRequest(self::$releaseJsonFeed), true);

        if (!is_array($releases)) {
            throw new Exception(
                'It seems like ' . self::$releaseJsonFeed .
                ' did not return the json string for the TYPO3 releases. Maybe it has been moved!?'
            );
        }

        $max = array();
        $stable = array();
		$security = array();
		$latestLts = $releases['latest_lts'];
        foreach ($releases as $major => $details) {
            if (is_array($details) && !empty($details['latest'])) {
                $max[$major] = $details['latest'];
            }

            if (is_array($details) && !empty($details['stable'])) {
                $stable[$major] = $details['stable'];
            }
            if (is_array($details) && is_array($details['releases'])) {
			    $found = false;
			    $latestCheckedVersion = '';
			    foreach ($details['releases'] as $version => $versionDetails) {
			        $latestCheckedVersion = $version;
                    // if major version > latest_lts check if bugfix part of version number == '0';
                    // in that case this is the security update and stop searching
                    if (version_compare($major, $latestLts, '>')) {
                        if ($versionDetails['type'] === 'security') {
                            $security[$major] = $version;
                            $found = true;
                            break;
                        }
                        // if versionDetails->type === 'security' then this is the security update; stop searching
                        if (self::extractBugfixNumberFromVersion($version) === 0) {
                            $security[$major] = $version;
                            $found = true;
                            break;
                        }
                        // if versionDetails->type === 'security' then this is the security update; stop searching
                    } elseif ($versionDetails['type'] === 'security') {
                        $security[$major] = $version;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
			        $security[$major] = $latestCheckedVersion;
                }
            }
        }
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Registry')->set('tx_caretaker', 'TYPO3versions', $max);
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Registry')->set('tx_caretaker', 'TYPO3versionsStable', $stable);
		\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Registry')->set('tx_caretaker', 'TYPO3versionsSecurity', $security);
        return true;
    }

    /**
     * @param bool $requestUrl
     * @return bool|mixed
     */
    protected static function curlRequest($requestUrl = false)
    {
        $curl = curl_init();
        if ($curl === false || $requestUrl === false) {
            return false;
        }

        curl_setopt($curl, CURLOPT_URL, $requestUrl);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $headers = array(
            'Cache-Control: no-cache',
            'Pragma: no-cache',
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    /**
     * extracts the bugfix part of a version number
     *
     * @param string $version
     * @return integer
     */
    protected static function extractBugfixNumberFromVersion($version)
    {
        $version = str_replace(array('_', ',', '-', '+'), '.', $version);
        $version = preg_replace('/(\d)([^\d\.])/', '$1.$2', $version);
        $version = preg_replace('/([^\d\.])(\d)/', '$1.$2', $version);
        $versionParts = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode('.', $version, true, 3);
        return $versionParts[2];
	}
}
