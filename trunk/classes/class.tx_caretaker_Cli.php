<?php
/**
 * This is a file of the caretaker project.
 * Copyright 2008 by n@work Internet Informationssystem GmbH (www.work.de)
 * 
 * @Author	Thomas Hempel 		<thomas@work.de>
 * @Author	Martin Ficzel		<martin@work.de>
 * @Author	Patrick Kollodzik	<patrick@work.de> 
 * @Author	Tobias Liebig   	<mail_typo3.org@etobi.de>
 * @Author	Christopher Hlubek	<hlubek@networkteam.com>
 * 
 * $Id$
 */

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008 Martin Ficzel <ficzel@work.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

if (!defined('TYPO3_cliMode'))  die('You cannot run this script directly!');

require_once(PATH_t3lib.'class.t3lib_cli.php');

require_once (t3lib_extMgm::extPath('caretaker') . '/classes/class.tx_caretaker_Helper.php');
require_once (t3lib_extMgm::extPath('caretaker') . '/classes/class.tx_caretaker_CliLogger.php');
require_once (t3lib_extMgm::extPath('caretaker') . '/classes/class.tx_caretaker_CliNotifier.php');
require_once (t3lib_extMgm::extPath('caretaker') . '/classes/class.tx_caretaker_ExtensionManagerHelper.php');

class tx_caretaker_Cli extends t3lib_cli {
	
	/**
	 * Constructor
	 */
    public function __construct () {

       		// Running parent class constructor
        parent::t3lib_cli();

       		// Setting help texts:
        $this->cli_help['name'] = 'Caretaker CLI-Testrunner';        
        $this->cli_help['synopsis'] = 'update|get|update-extension-list|help ###OPTIONS###';
        $this->cli_help['description'] = 'Class with basic functionality for CLI scripts';
        $this->cli_help['examples'] = '/.../cli_dispatch.phpsh caretaker update -i 6 -g 4';
        $this->cli_help['author'] = 'Martin Ficzel, (c) 2008';

        $this->cli_options[]=array('--root', 'update all beginning with Root Node');
        $this->cli_options[]=array('-A', 'Same as --root');        
        
        $this->cli_options[]=array('--instancegroup', 'Specify InstancegroupID to update');
        $this->cli_options[]=array('-I', 'Same as --instancegroup');        
        
        $this->cli_options[]=array('--instance', 'Specify InstanceID to update');
        $this->cli_options[]=array('-i', 'Same as --instance');
        
        $this->cli_options[]=array('--group', 'Specify TestgroupID to update');
        $this->cli_options[]=array('-g', 'Same as --group');
        
        $this->cli_options[]=array('--test', 'Specify TestID to update');
        $this->cli_options[]=array('-t', 'Same as --test');
        
        $this->cli_options[]=array('-f', 'force Refresh of testResults');        
        $this->cli_options[]=array('-r', 'Return status code');
    }
    
    /**
     * CLI engine
     *
     * @param    array        Command line arguments
     * @return    string
     */
	public function cli_main($argv) {
        $task = (string)$this->cli_args['_DEFAULT'][1];
		
         if (!$task) {
            $this->cli_validateArgs();
            $this->cli_help();
            exit;
        } 

        $logger = new tx_caretaker_CliLogger();        		
        if (isset($this->cli_args['-ss']) || isset($this->cli_args['-s']) || isset($this->cli_args['--silent'])) {
          	$logger->setSilentMode(TRUE);
        }
        
        if ($task == 'update' || $task == 'get') {
        	$force           = (boolean)$this->readArgument('--force','-f');
        	$return_status   = (boolean)$this->readArgument('-r');
        	
        	if ((boolean)$this->readArgument('--root', '-A')) {
        		$node = tx_caretaker_Helper::getRootNode();
        	} else {
        		$instancegroupID = (int)$this->readArgument('--instancegroup', '-I');
	        	$instanceID      = (int)$this->readArgument('--instance', '-i');
	        	$groupID         = (int)$this->readArgument('--group','-g');
	        	$testID          = (int)$this->readArgument('--test','-t');

	        	if (!($instancegroupID || $instanceID)) {
	        		$logger->log('Instance or Instancegroup must be specified');
	        	} else if ( $instancegroupID && $instanceID ) {
	        		$logger->log('Instance or Instancegroup must be specified');
	        	}
        	
        		$node = tx_caretaker_Helper::getNode($instancegroupID, $instanceID, $groupID, $testID);
        	}
        	
        	if ($node) {
        		$notifier = new tx_caretaker_CliNotifier();

        		$node->setNotifier($notifier);
        		$node->setLogger($logger);
        	
        		$res = FALSE;
	        	if ($task == 'update') {
		        	 $res = $node->updateTestResult($force);
	        	}
	        	
	        	if ($task == 'get') {
		        	 $res = $node->getTestResult();
	        	}
	        	
	        	$notifier->sendNotifications();
	        	
	        	if ($return_status) {
	        		$logger->log('State: ' . $res->getState() . ':' . $res->getStateInfo());
	        		exit ((int)$res->getState());
	        	} else {
	        		exit;
	        	}
        	} else {
        		/**
        		 * @todo tx_caretaker_cli::log doesnt exist, must be implemented
        		 */
        		$logger->log('Node not found or inactive');
        		exit;
        	}
        } elseif ($task == 'update-extension-list') {
        	$result = tx_caretaker_ExtensionManagerHelper::updateExtensionList();
        	$logger->log('Extension list update result: ' . $result);
        	exit;
        }
        
        if ($task == 'help') {
        	$this->cli_validateArgs();
			$this->cli_help();
			exit;
        }
    }
    
    /**
     * Get a spcific CLI Argument
     * 
     * @param string $name
     * @param string $alt_name
     * @return string
     */
    private function readArgument($name, $alt_name = FALSE) {
    	if ( $name &&  isset($this->cli_args[$name]) ) {
    		if ($this->cli_args[$name][0]) {
    			return $this->cli_args[$name][0];
    		} else {
    			return TRUE;
    		}
    	} else if  ($alt_name) {
    		return $this->readArgument($alt_name);
    	} else {
    		return FALSE;
    	}
    }
}

// Call the functionality
$sobe = t3lib_div::makeInstance('tx_caretaker_Cli');
$sobe->cli_main($_SERVER['argv']);

?>