<?php
namespace Caretaker\Caretaker\Tests\Unit;

use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
 */
class LatestVersionsHelperTest extends UnitTestCase
{

    /**
     * @var array
     */
    protected $releases;

    public function setup()
    {
        $json = file_get_contents(GeneralUtility::getFileAbsFileName('EXT:caretaker/Tests/Unit/Fixtures/versions.json'));
        $this->releases = json_decode($json, true);
    }

    public function testLatest()
    {
        $latest = \tx_caretaker_LatestVersionsHelper::getLatestFromReleases($this->releases);

        $this->assertEquals('3.0.2', $latest['max']['3'], 'v3: latest release');
        $this->assertEquals('3.0.1', $latest['stable']['3'], 'v3: stable release');
        $this->assertEquals('3.0.0', $latest['security']['3'], 'v3: no security release falls back to the first release');

        $this->assertEquals('2.1.2', $latest['max']['2'], 'v2: latest release');
        $this->assertEquals('2.1.2', $latest['stable']['2'], 'v2: stable release');
        $this->assertEquals('2.1.1', $latest['security']['2'], 'v2: security release');

        $this->assertEquals('1.1.2', $latest['max']['1'], 'v1: latest release');
        $this->assertEquals('1.1.2', $latest['stable']['1'], 'v1: stable release');
        $this->assertEquals('1.1.1', $latest['security']['1'], 'v1: security release');
    }
}
