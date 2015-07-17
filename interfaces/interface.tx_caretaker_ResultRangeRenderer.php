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
interface tx_caretaker_ResultRangeRenderer {

	/**
	 * Render the Result Range for this testNode
	 *
	 * @param string $filename
	 * @param tx_caretaker_TestResultRange $test_result_range
	 * @param string $title
	 * @param string $value_decription
	 * @return string HTML-Code to show the Chart
	 */
	public function renderTestResultRange($filename, $test_result_range, $title, $value_decription);

	/**
	 * Render the ResultRange
	 *
	 * @param string $filename
	 * @param tx_caretaker_AggregatorResultRange $aggregator_result_range
	 * @param string $itle
	 * @return string HTML-Code to show the Chart
	 */
	public function renderAggregatorResultRange($filename, $aggregator_result_range, $title);

	/**
	 * Render multiple TestResultRanges
	 *
	 * @param string $filename
	 * @param array $test_result_ranges
	 * @param array $titles
	 * @return string
	 */
	public function renderMultipleTestResultRanges($filename, $test_result_ranges, $titles);

}

?>