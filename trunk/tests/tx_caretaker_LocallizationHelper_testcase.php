<?php 

class tx_caretaker_LocallizationHelper_testcase extends tx_phpunit_testcase  {

	function test_locallization_of_non_lll_strings (){
		$str = 'foo';
		$lll = tx_caretaker_LocallizationHelper::locallizeString($str);
		$this->assertEquals( $str , $lll );
	}


	function test_locallization_of_whole_lll_strings (){
		$str = 'LLL:EXT:caretaker/tests/locallang-test.xml:foo';
		$lll = tx_caretaker_LocallizationHelper::locallizeString($str);
		$this->assertEquals( 'bar' , $lll );
		
	}

	function test_locallization_of_partial_lll_strings (){
		$str = 'foo {LLL:EXT:caretaker/tests/locallang-test.xml:foo} baz';
		$lll = tx_caretaker_LocallizationHelper::locallizeString($str);
		$this->assertEquals( 'foo bar baz' , $lll );

	}

	function test_locallization_of_multiple_lll_strings (){
		$str = 'foo {LLL:EXT:caretaker/tests/locallang-test.xml:foo} baz {LLL:EXT:caretaker/tests/locallang-test.xml:bar}{LLL:EXT:caretaker/tests/locallang-test.xml:foo}';
		$lll = tx_caretaker_LocallizationHelper::locallizeString($str);
		$this->assertEquals( 'foo bar baz bambar' , $lll );

	}


}

?>