<?php

class tx_caretaker_TestRunner_testcase extends tx_phpunit_testcase  {
	
	
	protected function setUp() {
		
	}
	
	protected function tearDown() {
		
	}
	
	public function provider_test_foo(){
		return array( 
			array( 1, 1, 'true is not true but i knew this before' ),
			array( 1, 1, 'true is not false add unit tests here'   ) 
		);
	}
	
	/**
	 * @dataProvider provider_test_foo
	 * @param unknown_type $foo
	 * @param unknown_type $bar
	 * @param unknown_type $baz
	 */
	public function test_foo( $foo, $bar, $baz ){
		$this->assertEquals( $foo, $bar, $baz );
	// 	$this->assertEquals( 1,1 );
	}
}

?>