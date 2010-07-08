<?php

/***************************************************************
* Copyright notice
*
* (c) 2009 by n@work GmbH and networkteam GmbH
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
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_LatestVersionsHelper {
	
	public static function updateLatestTypo3VersionRegistry() {
		
		$success = false;		

		$content = self::curlRequest('https://svn.typo3.org/TYPO3v4/Core/tags/');

		if (empty($content)) {
			return false;
		}

		preg_match_all('/TYPO3_(4-[0-9]{1,2}-[0-9]{1,2}((alpha|beta|RC)[^\/]{0,2})?)\//', $content, $matches);

		if (!is_array($matches[1]) || count($matches[1]) === 0) {
			return false;
		}
		
		$max = array();
		foreach ($matches[1] as $key => $version) {
			$versionDigits = explode('-', $version, 3);

			if ($max[$versionDigits[0] . '.' . $versionDigits[1]][2] < $versionDigits[2] && preg_match('/alpha|beta|RC/',$version) === 0) {
				$max[$versionDigits[0] . '.' . $versionDigits[1]] = $versionDigits;
			}
		}

		foreach ($max as $key => $value) {
			$max[$key] = implode('.', $value);
		}
		
		t3lib_div::makeInstance('t3lib_Registry')->set('tx_caretaker', 'TYPO3versions', $max);
		
		return true;
		
	}
	
	private function curlRequest($requestUrl = false) {
		$curl = curl_init();
        if ($curl === false || $requestUrl === false) {
        	return false;
        }

		curl_setopt($curl, CURLOPT_URL, $requestUrl);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		$headers = array(
            "Cache-Control: no-cache",
            "Pragma: no-cache"
        );
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		$response = curl_exec($curl);
		curl_close($curl);
		
		return $response;
	}
	
}