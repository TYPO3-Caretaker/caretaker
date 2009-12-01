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
 * The caretaker cli script.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
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
        $this->cli_options[]=array('-R', 'Same as --root');
        
        $this->cli_options[]=array('--node', 'update all beginning with this NodeID ');
        $this->cli_options[]=array('-N', 'Same as --node');        
        
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
        	$node            = false;

		$node_repository = tx_caretaker_NodeRepository::getInstance();
    	if ((boolean)$this->readArgument('--root', '-R')) {
				$node = $node_repository->getRootNode();
        	} else if ( $nodeId = $this->readArgument('--node', '-N') ) {
				$node = $node_repository->id2node($nodeId);
        	}
        	
        	if ($node) {

				$node->setLogger($logger);
        	
        		$res = FALSE;
	        	if ($task == 'update') {
		        	 $res = $node->updateTestResult($force);
	        	}
	        	
	        	if ($task == 'get') {
		        	 $res = $node->getTestResult();
	        	}
	        	       	
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
if (!defined('TYPO3_cliMode'))  die('You cannot run this script directly!');

$sobe = t3lib_div::makeInstance('tx_caretaker_Cli');
$sobe->cli_main($_SERVER['argv']);

?>