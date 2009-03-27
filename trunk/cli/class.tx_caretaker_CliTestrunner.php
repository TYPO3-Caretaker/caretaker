<?php

if (!defined('TYPO3_cliMode'))  die('You cannot run this script directly!');

require_once(PATH_t3lib.'class.t3lib_cli.php');
require_once (t3lib_extMgm::extPath('caretaker').'/classes/class.tx_caretaker_InstanceRepository.php');

class tx_caretaker_CliTestrunner extends t3lib_cli {
	
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
        
        $this->cli_options[]=array('--instance', 'Specify InstanceID to update');
        $this->cli_options[]=array('-i', 'Same as --instance');
        $this->cli_options[]=array('--group', 'Specify TestgroupID to update');
        $this->cli_options[]=array('-g', 'Same as --group');
        $this->cli_options[]=array('--test', 'Specify TestID to update');
        $this->cli_options[]=array('-t', 'Same as --test');
        $this->cli_options[]=array('-f', 'force Refresh of testResults');
                
    }
	
    function update($instanceID, $groupID, $testID ){
    	
    	$force = (isset($this->cli_args['-f'])||isset($this->cli_args['--force']));
    	
    	if ($instanceID > 0){
    		
  	    	$instance_repoistory    = tx_caretaker_InstanceRepository::getInstance();
			$instance = $instance_repoistory->getByUid($instanceID, $this);
			
			if ($instance) {

	    		if ($groupID){
	    			$group_repoistory    = tx_caretaker_GroupRepository::getInstance();
					$group = $group_repoistory->getByUid($groupID, $this);
					$res = $group->updateState($instance,$force);
	    		} else if ($testID) {
	    			$test_repoistory    = tx_caretaker_TestRepository::getInstance();
					$test = $test_repoistory->getByUid($testID, $this);
					$res = $test->updateState($instance,$force);
	    		} else {
					$res = $instance->updateState($force);
				}

    		} else {
				$this->cli_echo('instance '.$instanceID.' not found'.chr(10));
			}
    		
			
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
        
        if ($task = 'update'){
        	
        	
        	switch (true){
        		case ($this->cli_args['--instance'][0]>0):
        			$instanceID = (int)$this->cli_args['--instance'][0];
        			break;
        		case ($this->cli_args['-i'][0]):
        			$instanceID = (int)$this->cli_args['-i'][0];
        			break;
        		default:
        			$this->cli_echo('no instance was specified');
        			break;
        	}

        	switch (true){
        		case ($this->cli_args['--group'][0]>0):
        			$groupID = (int)$this->cli_args['--group'][0];
        			break;
        		case ($this->cli_args['-g'][0]>0):
        			$groupID = (int)$this->cli_args['-g'][0];
        			break;
        		default:
        			$groupID = false;
        			break;
        	}
        	
        	switch (true){
        		case ($this->cli_args['--test'][0]>0):
        			$testID = (int)$this->cli_args['--test'][0];
        			break;
        		case ($this->cli_args['-t'][0]>0):
        			$testID = (int)$this->cli_args['-t'][0];
        			break;
        		default:
        			$testID = false;
        			break;
        	}
        	
        	$this->update($instanceID, $groupID, $testID);
        	exit;
        }
         
    	if ($task = 'get'){
        	$instanceID = (int)$this->cli_args['-i'][0];
        	$groupID    = (int)$this->cli_args['-g'][0];
        	$testID     = (int)$this->cli_args['-g'][0];
        	$this->runTests($instanceID, $groupID, $testID);
        	exit;
        }
        
        if ($task = 'help'){
        	$this->cli_validateArgs();
			$this->cli_help();
			exit;
        }
		
    }
}

// Call the functionality
$GLOBALS['tx_caretaker_CliTestrunner'] = t3lib_div::makeInstance('tx_caretaker_CliTestrunner');
$GLOBALS['tx_caretaker_CliTestrunner']->cli_main($_SERVER['argv']);

?>