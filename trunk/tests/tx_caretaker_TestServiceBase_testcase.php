<?php

// require_once (t3lib_extMgm::extPath('caretaker').'/services/class.tx_caretaker_TestServiceBase.php');

class tx_caretaker_TestServiceBase_testcase extends tx_phpunit_testcase  {

	
	function test_flexform_configuration_works(){
		
		$test_service_base = new tx_caretaker_TestServiceBase;
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
		$this->assertEquals($test_service_base->getConfigValue('foo',123,'blah'), 123);
		$this->assertEquals($test_service_base->getConfigValue('bar',234), 123);
		$this->assertEquals($test_service_base->getConfigValue('baz',345), 345);
		$this->assertEquals($test_service_base->getConfigValue('blub'), false);
		$this->assertEquals($test_service_base->getConfigValue('blub',123,'sDemo'), 123);
		
		
	}
	
	
	function test_array_configuration_works(){
		$test_service_base = new tx_caretaker_TestServiceBase;
		
		$test_service_base->setConfiguration(array( 'foo' => 'bar', 'bar'=>123) );
		
		$this->assertEquals($test_service_base->getConfigValue('foo'), 'bar');
		$this->assertEquals($test_service_base->getConfigValue('bar',234), 123);
		$this->assertEquals($test_service_base->getConfigValue('baz',345), 345);
		
	}
	
}
?>
