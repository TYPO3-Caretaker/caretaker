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
interface tx_caretaker_TestServiceInterface
{
    /**
     * Initialize the Service
     */
    public function init();

    /**
     * Set the instance for the test execution
     *
     * @param tx_caretaker_InstanceNode $instance
     */
    public function setInstance($instance);

    /**
     * Set the configuration for this test
     *
     * @param array $configuration
     */
    public function setConfiguration($configuration);

    /**
     * Run the Test defined in TestConf and return a Testresult Object
     *
     * @return tx_caretaker_TestResult
     */
    public function runTest();

    /**
     * Get a short description for the meaning of the value in the current test
     *
     * @return string Description what is stored in the Value field.
     */
    public function getValueDescription();

    /**
     * Get information if the test service is able to execute tests
     *
     * @return bool
     */
    public function isExecutable();

    /**
     * @return string
     */
    public function getTypeDescription();

    /**
     * @return string
     */
    public function getConfigurationInfo();
}
