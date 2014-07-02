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
	'dependencies' => 'cms,tt_address',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => 'typo3temp/caretaker/charts',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'TYPO3_version' => '4.5.0-6.2.99',
	'PHP_version' => '5.2-',
	'author_company' => '',
	'version' => '0.5.0',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'tt_address' => '2.2.1-',
			'typo3' => '4.5.0-6.2.99',
			'php' => '5.2.0-'
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'css_styled_content' => '',
		),
	),
	'_md5_values_when_last_written' => 'a:5:{s:9:"ChangeLog";s:4:"9c48";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"1bdc";s:19:"doc/wizard_form.dat";s:4:"c49e";s:20:"doc/wizard_form.html";s:4:"c565";}',
);

?>
