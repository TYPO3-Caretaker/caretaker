<?php

if (!defined('TYPO3_cliMode'))  die('You cannot run this script directly!');

require_once(PATH_t3lib.'class.t3lib_cli.php');
require_once ('class.tx_caretaker_InstancegroupRepository.php');
require_once ('class.tx_caretaker_InstanceRepository.php');
require_once ('interface.tx_caretaker_LoggerInterface.php');


class tx_caretaker_CliTestrunner extends t3lib_cli implements tx_caretaker_LoggerInterface {
	
	/**
	 * Constructor
	 */
    function __construct () {

       		// Running parent class constructor
        parent::t3lib_cli();

       		// Setting help texts:
        $this->cli_help['name'] = 'Caretaker CLI-Testrunner';        
        $this->cli_help['synopsis'] = '###OPTIONS###';
        $this->cli_help['description'] = 'Class with basic functionality for CLI scripts';
        $this->cli_help['examples'] = '/.../cli_dispatch.phpsh caretaker update -i 6 -g 4';
        $this->cli_help['author'] = 'Martin Ficzel, (c) 2008';
        
        $this->cli_options[]=array('--instance instanceID', 'Specify InstanceID to update');
        $this->cli_options[]=array('-i instanceID', 'Same as --instance');
        $this->cli_options[]=array('--group', 'Specify TestgroupID to update');
        $this->cli_options[]=array('-g', 'Same as --group');
        $this->cli_options[]=array('--test', 'Specify TestID to update');
        $this->cli_options[]=array('-t', 'Same as --test');
        $this->cli_options[]=array('-f', 'force Refresh of testResults');
                
    }
	
   function updateInstancegroup( $instancegroupID, $force = false ){
    	
  	  	$instancegroup_repoistory    = tx_caretaker_InstancegroupRepository::getInstance();
		$instancegroup = $instancegroup_repoistory->getByUid($instancegroupID, $this);
		$instancegroup->setLogger($this);
		if ($instancegroup) {
			$res = $instancegroup->updateTestResult($force);
    	} else {
			$this->cli_echo('instancegroup '.$instancegroupID.' not found'.chr(10));
		}
    }
    
    function updateInstance( $instanceID, $groupID, $testID, $force = false ){
   	
      	$instance_repoistory    = tx_caretaker_InstanceRepository::getInstance();
		$instance = $instance_repoistory->getByUid($instanceID, $this);
		$instance->setLogger($this);
		
		if ($instance) {

    		if ($groupID){
    			$group_repoistory    = tx_caretaker_TestgroupRepository::getInstance();
				$group = $group_repoistory->getByUid($groupID, $instance);
				$res = $group->updateTestResult($force);
    		} else if ($testID) {
    			$test_repoistory    = tx_caretaker_TestRepository::getInstance();
				$test = $test_repoistory->getByUid($testID, $instance);
				$res = $test->updateTestResult($force);
    		} else {
				$res = $instance->updateTestResult($force);
			}
    	} else {
			$this->log('instance '.$instanceID.' not found'.chr(10));
		}
    }
    
    function log($msg){
    	$this->cli_echo($msg.chr(10));
    }
    
    function get($instanceID, $groupID, $testID){
    	$this->cli_echo('Get Instance '.$instanceID.' Group '.$groupID."\n");
    }
    
    /**
     * CLI engine
     *
     * @param    array        Command line arguments
     * @return    string
     */
    function cli_main($argv) {
    	
        $task = (string)$this->cli_args['_DEFAULT'][1];
		
         if (!$task){
            $this->cli_validateArgs();
            $this->cli_help();
            exit;
        } 
        
        if ($task == 'update'){
        	
       		$instancegroupID = (int)$this->readArgument('--instancegroup', '-I');
        	$instanceID      = (int)$this->readArgument('--instance', '-i');
        	$groupID         = (int)$this->readArgument('--group','-g');
        	$testID          = (int)$this->readArgument('--test','-t');
        	$force           = $this->readArgument('--force','-f');
        	
        	if (!($instancegroupID || $instanceID)) {
        		$this->log('Instance or Instancegroup must be specified');
        	} else if ( $instancegroupID && $instanceID ) {
        		$this->log('Instance or Instancegroup must be specified');
        	}
        	
        	if ($instanceID){
        		$this->updateInstance($instanceID, $groupID, $testID, $force);
        	} 
        	
        	if ($instancegroupID){
        		$this->updateInstancegroup($instancegroupID, $force);
        	}
        	
        	exit;
        }
        
        if ($task = 'help'){
        	$this->cli_validateArgs();
			$this->cli_help();
			exit;
        }
    }
    
    function readArgument($name, $alt_name=false){
    	if ( $name &&  isset($this->cli_args[$name]) ){
    		if ($this->cli_args[$name][0]) {
    			return $this->cli_args[$name][0];
    		} else {
    			return true;
    		}
    	} else if  ($alt_name) {
    		return $this->readArgument($alt_name);
    	} else {
    		return false;
    	}
    }
}

// Call the functionality
$GLOBALS['tx_caretaker_CliTestrunner'] = t3lib_div::makeInstance('tx_caretaker_CliTestrunner');
$GLOBALS['tx_caretaker_CliTestrunner']->cli_main($_SERVER['argv']);

?>