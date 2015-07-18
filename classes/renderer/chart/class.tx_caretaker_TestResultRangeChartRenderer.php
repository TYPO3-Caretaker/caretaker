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
 * Implementation of the rendering for the result range of a single test.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_TestResultRangeChartRenderer extends tx_caretaker_ChartRendererBase {

	/**
	 * the test result range to render
	 * @var tx_caretaker_TestResultRange
	 */
	var $testResultRange;

	/**
	 * information about the test result range
	 * @var array
	 */
	var $testResultRangeInfos;

	/**
	 * median result value
	 * @var float
	 */
	var $testResultRangeMedian;

	/**
	 * average result value
	 * @var float
	 */
	var $testResultRangeAverage;

	/**
	 * Set the test result range
	 * @param tx_caretaker_TestResultRange $testResultRange
	 */
	public function setTestResultRange(tx_caretaker_TestResultRange $testResultRange) {
		$this->testResultRange = $testResultRange;
		$this->testResultRangeInfos = $this->testResultRange->getInfos();
		$this->testResultRangeMedian = $this->testResultRange->getMedianValue();
		$this->testResultRangeAverage = $this->testResultRange->getAverageValue();

		$this->setStartTimestamp($this->testResultRange->getStartTimestamp());
		$this->setEndTimestamp($this->testResultRange->getEndTimestamp());

		$this->setMinValue($this->testResultRange->getMinValue());
		$this->setMaxValue($this->testResultRange->getMaxValue());

		$this->init();
	}

	/**
	 * draw the chart-background into the given chart image
	 * @param resource $image
	 */
	protected function drawChartImageBackground(&$image) {
		$lastX = NULL;
		$lastState = NULL;
		$count = $this->testResultRange->count();
		$step = 0;
		$backgroundColor = 0;
		/**
		 * @var mixed $key
		 * @var tx_caretaker_TestResult $testResult
		 */
		foreach ($this->testResultRange as $key => $testResult) {
			$step++;

			$newX = intval($this->transformX($testResult->getTimestamp()));
			$newState = $testResult->getState();

			if ($lastX !== NULL) {
				switch ($lastState) {
					case tx_caretaker_Constants::state_ok:
						$colorRGB = $this->getColorRgbByKey("OK");
						break;
					case tx_caretaker_Constants::state_warning:
						$colorRGB = $this->getColorRgbByKey("WARNING");
						break;
					case tx_caretaker_Constants::state_error:
						$colorRGB = $this->getColorRgbByKey("ERROR");
						break;
					case tx_caretaker_Constants::state_due:
						$colorRGB = $this->getColorRgbByKey("DUE");
						break;
					case tx_caretaker_Constants::state_ack:
						$colorRGB = $this->getColorRgbByKey("ACK");
						break;
					default:
						$colorRGB = $this->getColorRgbByKey("UNDEFINED");
						break;
				}
				$backgroundColor = imagecolorallocatealpha($image, $colorRGB[0], $colorRGB[1], $colorRGB[2], 100);
			}

			$isLast = ($step == $count);

			if ($lastX !== NULL && $backgroundColor && ($newState != $lastState || $isLast)) {
				imagefilledrectangle($image, $lastX, $this->marginTop, $newX, $this->height - $this->marginBottom, $backgroundColor);
			}

			if ($newState !== $lastState) {
				$lastX = $newX;
			}
			$lastState = $newState;
		}
	}

	/**
	 * draw the chart-foreground into the given chart image
	 * @param resource $image
	 */
	protected function drawChartImageForeground(&$image) {
		$colorBg = imagecolorallocatealpha($image, 0, 0, 255, 100);
		$color = imagecolorallocate($image, 0, 0, 255);
		$lastX = NULL;
		$lastY = NULL;
		$bgPoints = array();
		$feLines = array();

		/** @var tx_caretaker_TestResult $testResult */
		foreach ($this->testResultRange as $testResult) {
			$newX = intval($this->transformX($testResult->getTimestamp()));
			$newY = intval($this->transformY($testResult->getValue()));
			if ($lastX !== NULL) {
				// bg
				$bgPoints[] = $lastX;
				$bgPoints[] = $lastY;
				$bgPoints[] = $newX;
				$bgPoints[] = $lastY;
				$bgPoints[] = $newX;
				$bgPoints[] = $newY;

				// fe
				$feLines[] = array($lastX, $lastY, $newX, $lastY);
				$feLines[] = array($newX, $lastY, $newX, $newY);

			}
			$lastX = $newX;
			$lastY = $newY;
		}

		$bgPoints[] = intval($this->transformX($this->testResultRange->getLast()->getTimestamp()));
		$bgPoints[] = intval($this->transformY(0));
		$bgPoints[] = intval($this->transformX($this->testResultRange->getFirst()->getTimestamp()));
		$bgPoints[] = intval($this->transformY(0));

		// draw filled chart background
		if (count($bgPoints) > 7) {
			imagefilledpolygon($image, $bgPoints, count($bgPoints) / 2, $colorBg);
		}
		// draw line
		if (count($feLines) > 1) {
			foreach ($feLines as $line) {
				imageline($image, $line[0], $line[1], $line[2], $line[3], $color);
			}
		}
	}

	/**
	 * Get the title to display in the chart.
	 * @return string
	 */
	protected function getChartTitle() {
		$title = $this->title . ' ' . round(($this->testResultRangeInfos['PercentAVAILABLE'] * 100), 2) . "% available";
		if ($this->testResultRangeMedian != 0 || $this->testResultRangeAverage != 0) {
			$title .= ' [Median: ' . number_format($this->testResultRangeMedian, 2) . ', Average: ' . number_format($this->testResultRangeAverage, 2) . ']';
		}
		return $title;
	}

	/**
	 * draw the chart-legend into the given chart image
	 * @param resource $image
	 */
	protected function drawChartImageLegend(&$image) {
		$chartLegendColor = imagecolorallocate($image, 1, 1, 1);
		$legendItems = array(
				'OK' => $this->testResultRangeInfos['PercentOK'],
				'Warning' => $this->testResultRangeInfos['PercentWARNING'],
				'Error' => $this->testResultRangeInfos['PercentERROR'],
				'Undefined' => $this->testResultRangeInfos['PercentUNDEFINED'],
				'ACK' => $this->testResultRangeInfos['PercentACK'],
				'DUE' => $this->testResultRangeInfos['PercentDUE']
		);

		$offset = $this->marginTop + 10;

		/**
		 * @var string $key
		 * @var float $value
		 */
		foreach ($legendItems as $key => $value) {
			$colorRGB = $this->getColorRgbByKey($key);
			$itemColor = imagecolorallocate($image, $colorRGB[0], $colorRGB[1], $colorRGB[2]);

			$x = $this->width - $this->marginRight + 20;
			$y = $offset;

			imagefilledrectangle($image, $x - 5, $y - 8, $x, $y - 3, $itemColor);
			imagerectangle($image, $x - 5, $y - 8, $x, $y - 3, $chartLegendColor);

			$font = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('caretaker') . '/lib/Fonts/tahoma.ttf';
			$size = 9;
			$angle = 0;
			imagettftext($image, $size, $angle, $x + 10, $y, $chartLegendColor, $font, $key . ' ' . number_format($value * 100, 2) . ' %');

			$offset += 18;
		}
	}
}
