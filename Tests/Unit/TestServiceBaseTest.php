<?php
namespace Caretaker\Caretaker\Tests\Unit;

use TYPO3\CMS\Core\Tests\UnitTestCase;

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
class TestServiceBaseTest extends UnitTestCase
{
    public function test_flexform_configuration_works()
    {
        $this->markTestIncomplete('stub ->setConfiguration()');

        $test_service_base = new \tx_caretaker_TestServiceBase();
        $test_service_base->setConfiguration(
            '<?xml version="1.0" encoding="iso-8859-1" standalone="yes" ?>
			<T3FlexForms>
			    <data>
			        <sheet index="sDEF">
			            <language index="lDEF">
			                <field index="foo">
			                    <value index="vDEF">bar</value>
			                </field>
			                <field index="bar">
			                    <value index="vDEF">123</value>
			                </field>
			            </language>
			        </sheet>
			        <sheet index="sDemo">
			        	<language index="lDEF">
			                <field index="baz">
			                    <value index="vDEF">blub</value>
			                </field>
			            </language>
					</sheet>
			    </data>
			</T3FlexForms>'
        );

        $this->assertEquals($test_service_base->getConfigValue('foo'), 'bar');
        $this->assertEquals($test_service_base->getConfigValue('foo', 123, 'blah'), 123);
        $this->assertEquals($test_service_base->getConfigValue('bar', 234), 123);
        $this->assertEquals($test_service_base->getConfigValue('baz', 345), 345);
        $this->assertEquals($test_service_base->getConfigValue('blub'), false);
        $this->assertEquals($test_service_base->getConfigValue('blub', 123, 'sDemo'), 123);
    }

    public function test_array_configuration_works()
    {
        $test_service_base = new \tx_caretaker_TestServiceBase();

        $test_service_base->setConfiguration(array('foo' => 'bar', 'bar' => 123));

        $this->assertEquals($test_service_base->getConfigValue('foo'), 'bar');
        $this->assertEquals($test_service_base->getConfigValue('bar', 234), 123);
        $this->assertEquals($test_service_base->getConfigValue('baz', 345), 345);
    }
}
