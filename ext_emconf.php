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

########################################################################
# Extension Manager/Repository config file for ext: "caretaker"
#
# Auto generated 27-08-2008 08:58
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
		'title' => 'Caretaker',
		'description' => 'Server for caretaker observation system',
		'category' => 'misc',
		'author' => 'Martin Ficzel,Thomas Hempel,Christopher Hlubek,Tobias Liebig',
		'author_email' => 'ficzel@work.de,hempel@work.de,hlubek@networkteam.com,typo3@etobi.de',
		'shy' => '',
		'dependencies' => 'cms',
		'conflicts' => '',
		'priority' => '',
		'module' => '',
		'state' => 'stable',
		'internal' => '',
		'uploadfolder' => 0,
		'createDirs' => 'typo3temp/caretaker/charts',
		'modify_tables' => '',
		'clearCacheOnLoad' => 0,
		'lockType' => '',
		'TYPO3_version' => '6.2.0-7.99.99',
		'PHP_version' => '5.3.0-',
		'author_company' => '',
		'version' => '0.7.6',
		'constraints' => array(
				'depends' => array(
						'cms' => '',
						'caretaker_instance' => '0.7.0-',
						'typo3' => '6.2.0-7.99.99',
						'php' => '5.3.0-'
				),
				'conflicts' => array(),
				'suggests' => array(),
		),
		'autoload' => array(
				'classmap' => array(
						'Classes',
						'interfaces',
						'scheduler',
						'pi_abstract',
						'pi_base',
						'pi_graphreport',
						'pi_overview',
						'pi_singleview',
				)
		),
		'_md5_values_when_last_written' => '',
);
