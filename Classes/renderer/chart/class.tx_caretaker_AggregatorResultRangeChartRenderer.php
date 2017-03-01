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
class tx_caretaker_AggregatorResultRangeChartRenderer extends tx_caretaker_ChartRendererBase
{

    /**
     * the result range to render
     *
     * @var tx_caretaker_AggregatorResultRange
     */
    var $aggregatorResultRange = [];

    /**
     * informations about the current result range
     *
     * @var array
     */
    var $aggregatorResultRangeInfos = [];

    /**
     * Set the result Range
     *
     * @param tx_caretaker_AggregatorResultRange $aggregatorResultRange
     */
    public function setAggregatorResultrange(tx_caretaker_AggregatorResultRange $aggregatorResultRange)
    {
        $this->aggregatorResultRange = $aggregatorResultRange;
        $this->aggregatorResultRangeInfos = $this->aggregatorResultRange->getInfos();
        $this->setStartTimestamp($this->aggregatorResultRange->getStartTimestamp());
        $this->setEndTimestamp($this->aggregatorResultRange->getEndTimestamp());

        $maxValue = 0;
        /** @var tx_caretaker_AggregatorResult $aggregatorResult */
        foreach ($this->aggregatorResultRange as $aggregatorResult) {
            $undefined = $aggregatorResult->getNumUNDEFINED();
            $ok = $aggregatorResult->getNumOK();
            $warning = $aggregatorResult->getNumWARNING();
            $error = $aggregatorResult->getNumERROR();

            $count = $undefined + $ok + $warning + $error;
            if ($count > $maxValue) {
                $maxValue = $count;
            }
        }

        $this->setMinValue(0);
        $this->setMaxValue($maxValue);

        $this->init();
    }

    /**
     * Get the title to display in the chart.
     *
     * @return string
     */
    protected function getChartTitle()
    {
        return $this->getTitle() . ' ' . round(($this->aggregatorResultRangeInfos['PercentAVAILABLE'] * 100), 2) . "% " . "available";
    }

    /**
     * draw the chart-background into the given chart image
     *
     * @param resource $image
     */
    protected function drawChartImageBackground(&$image)
    {
        // nothing needed here
    }

    /**
     * draw the chart-foreground into the given chart image
     *
     * @param resource $image
     */
    protected function drawChartImageForeground(&$image)
    {
        $color = imagecolorallocate($image, 0, 0, 255);

        /** @var tx_caretaker_AggregatorResult $lastResult */
        $lastResult = null;
        $lastX = 0;
        $lastY = 0;
        $startX = 0;
        $startY = 0;

        /** @var tx_caretaker_AggregatorResult $aggregatorResult */
        foreach ($this->aggregatorResultRange as $aggregatorResult) {
            if ($lastResult !== null) {
                $total = 0;
                foreach (['OK', 'WARNING', 'ERROR', 'UNDEFINED'] as $state) {
                    $number = $lastResult->getNumGENERIC($state);
                    if ($number > 0) {
                        $colorRGB = $this->getColorRgbByKey($state);
                        $itemColor = imagecolorallocate($image, $colorRGB[0], $colorRGB[1], $colorRGB[2]);
                        $itemColorAlpha = imagecolorallocatealpha($image, $colorRGB[0], $colorRGB[1], $colorRGB[2], 90);

                        $startX = intval($this->transformX($lastResult->getTimestamp()));
                        $stopX = intval($this->transformX($aggregatorResult->getTimestamp()));

                        $startY = intval($this->transformY($total + $number));
                        $stopY = intval($this->transformY($total));

                        imagefilledrectangle($image, $startX, $startY, $stopX, $stopY, $itemColorAlpha);
                        imageline($image, $startX, $startY, $stopX, $startY, $itemColor);

                        $total += $number;
                    }
                }
                imageline($image, $lastX, $lastY, $startX, $lastY, $color);
                imageline($image, $startX, $lastY, $startX, $startY, $color);
            }

            $lastX = $startX;
            $lastY = $startY;

            $lastResult = $aggregatorResult;
        }
    }

    /**
     * draw the chart-legend into the given chart image
     *
     * @param resource $image
     */
    protected function drawChartImageLegend(&$image)
    {
        $chartLegendColor = imagecolorallocate($image, 1, 1, 1);

        $legendItems = [
            'OK' => $this->aggregatorResultRangeInfos['PercentOK'],
            'Warning' => $this->aggregatorResultRangeInfos['PercentWARNING'],
            'Error' => $this->aggregatorResultRangeInfos['PercentERROR'],
            'Undefined' => $this->aggregatorResultRangeInfos['PercentUNDEFINED'],
        ];

        $offset = $this->marginTop + 10;

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
