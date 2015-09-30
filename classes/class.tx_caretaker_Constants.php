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

/**
 * Class that defines a set of connstants that are used in caretaker
 *
 * @author Thomas Hempel <thomas@work.de>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_Constants {

		/* result states */
	const state_ok = 0;
	const state_warning = 1;
	const state_error = 2;
	const state_undefined = -1;
	const state_ack = -2;
	const state_due = -3;


		/* node types */
	const nodeType_Root =			'Root';
	const nodeType_Instancegroup =	'Instancegroup';
	const nodeType_Instance =		'Instance';
	const nodeType_Testgroup =		'Testgroup';
	const nodeType_Test =			'Test';

		/* tables */
		/* foreign tables */
	const table_TTAddressAddresses = 'tt_address';
	const table_ContactAddresses = 'tx_caretaker_contactaddress';

		/* data tables */
	const table_Instances =			'tx_caretaker_instance';
	const table_Instancegroups =	'tx_caretaker_instancegroup';
	const table_Testgroups =		'tx_caretaker_testgroup';
	const table_Tests =				'tx_caretaker_test';
	const table_Testresults =		'tx_caretaker_testresult';
	const table_Lasttestresults =	'tx_caretaker_lasttestresult';
	const table_Aggregatorresults = 'tx_caretaker_aggregatorresult';
	const table_Roles =				'tx_caretaker_roles';
	const table_Exitponts =			'tx_caretaker_exitpoints';
	const table_Strategies =		'tx_caretaker_strategies';

		/* relation tables */
	const relationTable_Node2Address =		 'tx_caretaker_node_address_mm';
	const relationTable_Node2Strategy =		 'tx_caretaker_node_strategy_mm';
	const relationTable_Test2Roles =		 'tx_caretaker_test_roles_mm';
	const relationTable_Instance2Testgroup = 'tx_caretaker_instance_testgroup_mm';
	const relationTable_Testgroup2Test =	 'tx_caretaker_testgroup_test_mm';
	const relationTable_Instance2Test =		 'tx_caretaker_instance_test_mm';
}
