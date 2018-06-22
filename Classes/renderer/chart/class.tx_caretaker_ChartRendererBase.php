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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

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
 * Baseclass for all Chart Renderers.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 */
abstract class tx_caretaker_ChartRendererBase
{
    /**
     * width of the whole chart
     *
     * @var int
     */
    protected $width = 800;

    /**
     * height of the whole chart
     *
     * @var int
     */
    protected $height = 400;

    /**
     * width of the chart area
     *
     * @var int
     */
    protected $chartWidth;

    /**
     * height of the chart area
     *
     * @var int
     */
    protected $chartHeight;

    /**
     * x scale factor
     *
     * @var float
     */
    protected $chartFactorX;

    /**
     * y scale factor
     *
     * @var float
     */
    protected $chartFactorY;

    /**
     * minimal Value
     *
     * @var float
     */
    protected $minValue = 0;

    /**
     * maximal Value
     *
     * @var float
     */
    protected $maxValue = 1;

    /**
     * start timestamp
     *
     * @var int
     */
    protected $startTimestamp;

    /**
     * emd timestamp
     *
     * @var int
     */
    protected $endTimestamp;

    /**
     * Title
     *
     * @var string
     */
    protected $title;

    /**
     * margin between charting area and border
     *
     * @var int
     */
    protected $marginLeft = 70;

    /**
     * margin between charting area and border
     *
     * @var int
     */
    protected $marginRight = 150;

    /**
     * margin between charting area and border
     *
     * @var int
     */
    protected $marginTop = 30;

    /**
     * margin between charting area and border
     *
     * @var int
     */
    protected $marginBottom = 80;

    /**
     * @var float
     */
    protected $scaleX;

    /**
     * @var float
     */
    protected $scaleY;

    /**
     * @var int
     */
    protected $baseX;

    /**
     * @var int
     */
    protected $baseY;

    /**
     * Constructor
     *
     * @param int $width
     * @param int $height
     */
    public function __construct($width = 800, $height = 400)
    {
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Set the start timestamp for the chart
     *
     * @param int $startTimestamp
     */
    protected function setStartTimestamp($startTimestamp)
    {
        $this->startTimestamp = $startTimestamp;
    }

    /**
     * Get the start timestamp of the chart
     *
     * @return int
     */
    protected function getStartTimestamp()
    {
        return $this->startTimestamp;
    }

    /**
     * Set the end timestamp of the chart
     *
     * @param int $endTimestamp
     */
    protected function setEndTimestamp($endTimestamp)
    {
        $this->endTimestamp = $endTimestamp;
    }

    /**
     * Get the end timestamp of the chart
     *
     * @return int
     */
    protected function getEndTimestamp()
    {
        return $this->endTimestamp;
    }

    /**
     * Set the minimum chart value
     *
     * @param float $minValue
     */
    protected function setMinValue($minValue)
    {
        $this->minValue = $minValue;
    }

    /**
     * Get the selected minimum chart value
     *
     * @return float
     */
    protected function getMinValue()
    {
        return $this->minValue;
    }

    /**
     * Set the maximum chart value
     *
     * @param float $maxValue
     */
    protected function setMaxValue($maxValue)
    {
        if ($maxValue <= 0) {
            $maxValue = 1;
        }
        $this->maxValue = $maxValue * 1.05;
    }

    /**
     * Get the selected maximum chart value
     *
     * @return float
     */
    protected function getMaxValue()
    {
        return $this->maxValue;
    }

    /**
     * Set the title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get the title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the title to display in the chart.
     *
     * @return string
     */
    abstract protected function getChartTitle();

    /**
     * draw the chart-background into the given chart image
     *
     * @param resource $image
     */
    abstract protected function drawChartImageBackground(&$image);

    /**
     * draw the chart-foreground into the given chart image
     *
     * @param resource $image
     */
    abstract protected function drawChartImageForeground(&$image);

    /**
     * draw the chart-legend into the given chart image
     *
     * @param resource $image
     */
    abstract protected function drawChartImageLegend(&$image);

    /**
     * init the chart and calculate the scales
     */
    protected function init()
    {
        // calculate chart Area
        $this->chartWidth = $this->width - $this->marginLeft - $this->marginRight;
        $this->chartHeight = $this->height - $this->marginTop - $this->marginBottom;

        // calculate the ranges
        $this->scaleX = $this->chartWidth / ($this->endTimestamp - $this->startTimestamp);
        $this->scaleY = $this->chartHeight / ($this->maxValue);

        $this->baseX = $this->startTimestamp;
        $this->baseY = 0;
    }

    /**
     * transform values from valuespace to chart-image space
     *
     * @param float $value
     * @return float
     */
    protected function transformX($value)
    {
        return $this->marginLeft + ($value - $this->baseX) * $this->scaleX;
    }

    /**
     * transform values from valuespace to chart-image space
     *
     * @param float $value
     * @return float
     */
    protected function transformY($value)
    {
        return $this->marginTop + $this->chartHeight - (($value - $this->baseY) * $this->scaleY);
    }

    /**
     * combine the chart images from the given parts
     */
    public function getChartImage()
    {
        // create image of width and height
        $image = imagecreatetruecolor($this->width, $this->height);
        // imageantialias ( $image , true );

        // Make the background transparent
        $background = imagecolorallocate($image, 255, 255, 255);
        imagefilledrectangle($image, 0, 0, $this->width, $this->height, $background);
        imagecolortransparent($image, $background);

        // fill chart area
        $chartBackgroundColor = imagecolorallocate($image, 254, 254, 254);
        $chartLegendColor = imagecolorallocate($image, 1, 1, 1);
        $chartLegendColor2 = imagecolorallocatealpha($image, 1, 1, 1, 60);
        $chartLegendColor3 = imagecolorallocatealpha($image, 1, 1, 1, 90);

        imagefilledrectangle($image, $this->marginLeft, $this->marginTop, $this->width - $this->marginRight, $this->height - $this->marginBottom, $chartBackgroundColor);

        // combine with chartImageBackground
        $this->drawChartImageBackground($image);

        // combine with chart axes
        imagerectangle($image, $this->marginLeft, $this->marginTop, $this->width - $this->marginRight, $this->height - $this->marginBottom, $chartLegendColor);
        $this->drawYAxis($image, $chartLegendColor, $chartLegendColor2);
        $this->drawXAxis($image, $chartLegendColor, $chartLegendColor2, $chartLegendColor3);

        // combine with getChartImageForeground
        $this->drawChartImageForeground($image);

        // combine with getChartImageLegend
        $this->drawChartImageLegend($image);

        // combine with getChartImageTitle
        $this->drawChartTitle($image, $this->getChartTitle());

        return $image;
    }

    /**
     * Get the chart image as a png file
     *
     * @param string $filename
     * @return string filename of png file
     */
    public function getChartImagePng($filename)
    {
        $absoluteFilename = PathUtility::isAbsolutePath($file) ? $filename : PATH_site . ltrim($filename, '/');
        if (!file_exists(dirname($absoluteFilename))) {
            GeneralUtility::mkdir_deep(dirname($absoluteFilename));
        }
        $image = $this->getChartImage();
        imagepng($image, $absoluteFilename);
        imagedestroy($image);

        return $filename;
    }

    /**
     * Write the chart image and return the appropriate image tag
     *
     * @param string $filename
     * @param string $baseUrl
     * @return string Image Tag
     */
    public function getChartImageTag($filename, $baseUrl = '')
    {
        $imagePng = $this->getChartImagePng($filename);

        return '<img  src="' . $baseUrl . $imagePng . '" width="' . $this->width . '" height="' . $this->height . '" />';
    }

    /**
     * Draw the title of the chart
     *
     * @param resource $image
     * @param <type> $chartLegendColor
     * @param <type> $title
     */
    private function drawChartTitle(&$image, $title)
    {
        $font = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('caretaker') . '/lib/Fonts/tahoma.ttf';
        $size = 9;
        $angle = 0;

        $position = imageftbbox($size, $angle, $font, $title);
        $textWidth = abs($position[2]) - abs($position[0]);

        $color = imagecolorallocate($image, 1, 1, 1);
        imagettftext($image, $size, $angle, $this->marginLeft + floor($this->chartWidth / 2) - $textWidth / 2, 20, $color, $font, $title);
    }

    /**
     * Draw the X-Axis Visualisation and Value Information into the chart image
     *
     * @param resource $image
     * @param int $chartLegendColor
     * @param int $chartLegendColor2
     */
    private function drawYAxis(&$image, &$chartLegendColor, &$chartLegendColor2)
    {
        // detect the y axis separation
        $rounded_value = $this->ceilDecimal($this->maxValue);

        if ($rounded_value > $this->maxValue * 5) {
            $value_step = $rounded_value / 40;
        } elseif ($rounded_value > $this->maxValue * 2) {
            $value_step = $rounded_value / 20;
        } else {
            $value_step = $rounded_value / 10;
        }

        for ($value = 0; $value <= $this->maxValue; $value += $value_step) {
            // line
            $scaledValueY = intval($this->transformY($value));
            imageline($image, $this->marginLeft - 3, $scaledValueY, $this->width - $this->marginRight, $scaledValueY, $chartLegendColor2);

            // text
            $font = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('caretaker') . '/lib/Fonts/tahoma.ttf';
            $size = 9;
            $angle = 0;

            $position = imageftbbox($size, $angle, $font, $value);
            $textWidth = abs($position[2]) - abs($position[0]);

            imagecolorallocate($image, 255, 1, 1);

            imagettftext($image, $size, $angle, $this->marginLeft - 10 - floor($textWidth), $scaledValueY + 6, $chartLegendColor, $font, $value);
        }
    }

    /**
     * Draw the X-Axis Visualisation and Value Information
     *
     * @param resource $image
     * @param int $chartLegendColor
     * @param int $chartLegendColor2
     * @param int $chartLegendColor3
     */
    private function drawXAxis(&$image, &$chartLegendColor, &$chartLegendColor2, &$chartLegendColor3)
    {
        $timerange = $this->endTimestamp - $this->startTimestamp;

        $times_super = array();
        $times_major = array();
        $times_minor = array();

        $format = '%x';

        // year
        if ($timerange >= 24 * 60 * 60 * 30 * 6) {
            $times_super = $this->getYearTimestamps($this->startTimestamp, $this->endTimestamp);
            $times_major = $this->getMonthTimestamps($this->startTimestamp, $this->endTimestamp);
        } // quarter
        elseif ($timerange >= 24 * 60 * 60 * 30 * 3) {
            $times_super = $this->getYearTimestamps($this->startTimestamp, $this->endTimestamp);
            $times_major = $this->getMonthTimestamps($this->startTimestamp, $this->endTimestamp);
            $times_minor = $this->getWeekTimestamps($this->startTimestamp, $this->endTimestamp);
        } // 1 Month
        elseif ($timerange >= 24 * 60 * 60 * 30) {
            $times_super = $this->getMonthTimestamps($this->startTimestamp, $this->endTimestamp);
            $times_major = $this->getWeekTimestamps($this->startTimestamp, $this->endTimestamp);
            $times_minor = $this->getDayTimestamps($this->startTimestamp, $this->endTimestamp);
        } // 7 days
        elseif ($timerange >= 24 * 60 * 60 * 7) {
            $times_super = $this->getMonthTimestamps($this->startTimestamp, $this->endTimestamp);
            $times_major = $this->getWeekTimestamps($this->startTimestamp, $this->endTimestamp);
            $times_minor = $this->getDayTimestamps($this->startTimestamp, $this->endTimestamp);
        } // 2 days
        elseif ($timerange >= 24 * 60 * 60 * 2) {
            $format = '%x (%H)';
            $times_super = $this->getDayTimestamps($this->startTimestamp, $this->endTimestamp);
            $times_major = $this->getHalfdayTimestamps($this->startTimestamp, $this->endTimestamp);
            $times_minor = $this->getHourTimestamps($this->startTimestamp, $this->endTimestamp);
        } // 1 day
        elseif ($timerange >= 24 * 60 * 60 * 1) {
            $format = '%H:%M';
            $times_super = $this->getDayTimestamps($this->startTimestamp, $this->endTimestamp);
            $times_major = $this->getHalfdayTimestamps($this->startTimestamp, $this->endTimestamp);
            $times_minor = $this->getHourTimestamps($this->startTimestamp, $this->endTimestamp);
        } // < 1 day
        else {
            $format = '%H:%M';
            $times_super = $this->getHalfdayTimestamps($this->startTimestamp, $this->endTimestamp);
            $times_major = $this->getHourTimestamps($this->startTimestamp, $this->endTimestamp);
            $times_minor = $this->getQuarterTimestamps($this->startTimestamp, $this->endTimestamp);
        }

        // draw lines
        foreach ($times_super as $timestamp) {
            if ($timestamp > $this->startTimestamp && $timestamp < $this->endTimestamp) {
                $scaledTime = $this->transformX($timestamp);
                imageline($image, $scaledTime, $this->marginTop, $scaledTime, $this->height - $this->marginBottom + 3, $chartLegendColor);
            }
        }

        foreach ($times_major as $timestamp) {
            if ($timestamp > $this->startTimestamp && $timestamp < $this->endTimestamp) {
                $scaledTime = $this->transformX($timestamp);
                imageline($image, $scaledTime, $this->marginTop, $scaledTime, $this->height - $this->marginBottom + 3, $chartLegendColor2);
            }
        }

        foreach ($times_minor as $timestamp) {
            if ($timestamp > $this->startTimestamp && $timestamp < $this->endTimestamp) {
                $scaledTime = $this->transformX($timestamp);
                imageline($image, $scaledTime, $this->marginTop, $scaledTime, $this->height - $this->marginBottom, $chartLegendColor3);
            }
        }

        // draw x - axis informations
        if (count($times_super) > 3) {
            $x_axis = $times_super;
        } elseif (count($times_major) > 3) {
            $x_axis = $times_major;
        } else {
            $x_axis = $times_minor;
        }

        foreach ($x_axis as $timestamp) {
            $this->transformX($timestamp);
            $font = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('caretaker') . '/lib/Fonts/tahoma.ttf';
            $size = 9;
            $angle = 45;
            $info = strftime($format, $timestamp);

            $position = imageftbbox($size, $angle, $font, $info);
            $textWidth = abs($position[2]) - abs($position[0]);
            $textHeight = abs($position[1]) - abs($position[3]);

            $scaledTime = $this->transformX($timestamp);

            imagettftext($image, $size, $angle, floor($scaledTime) - $textWidth, $this->height - $this->marginBottom - $textHeight + 10, $chartLegendColor, $font, $info);
        }
    }

    /**
     * round a decimal value to show 2 significant numbers
     *
     * @param float $value
     * @return float|int
     */
    private function ceilDecimal($value)
    {
        $number = (float)$value;

        if ($number > 1 || $number < -1) {
            $abs_str = (string)round(abs($number));
            $significance = pow(10, (int)strlen($abs_str));
        } elseif ($value == 0) {
            return 1;
        } else {
            $abs_str = (string)abs($number);
            $pos = 0;
            while (substr($abs_str, $pos, 1) == '0' || substr($abs_str, $pos, 1) == '.') {
                $pos++;
            }
            $significance = pow(10, 1 + $pos * -1);
        }

        if ($number > 0) {
            $result = ceil($number / $significance) * $significance;
        } else {
            $result = -1 * ceil(abs($number) / $significance) * $significance;
        }

        return $result;
    }

    /**
     * Get all news-year timestamps in the given range
     *
     * @param int $min_timestamp
     * @param int $max_timestamp
     * @return array
     */
    private function getYearTimestamps($min_timestamp, $max_timestamp)
    {
        $result = array();
        $startdate_info = getdate($min_timestamp);
        $year = $startdate_info['year'];
        while ($max_timestamp > $ts = mktime(0, 0, 0, 1, 1, $year)) {
            if ($ts > $min_timestamp) {
                $result[] = $ts;
            }
            $year++;
        }

        return $result;
    }

    /**
     * Get all month timestamps in the given range
     *
     * @param int $min_timestamp
     * @param int $max_timestamp
     * @return array
     */
    private function getMonthTimestamps($min_timestamp, $max_timestamp)
    {
        $result = array();
        $startdate_info = getdate($min_timestamp);
        $year = $startdate_info['year'];
        $month = $startdate_info['mon'];
        while ($max_timestamp > $ts = mktime(0, 0, 0, $month, 1, $year)) {
            if ($ts > $min_timestamp) {
                $result[] = $ts;
            }
            $month++;
        }

        return $result;
    }

    /**
     * Get all week(sunday noon) timestamps in the given range
     *
     * @param int $min_timestamp
     * @param int $max_timestamp
     * @return array
     */
    private function getWeekTimestamps($min_timestamp, $max_timestamp)
    {
        $result = array();
        $startdate_info = getdate($min_timestamp);
        $year = $startdate_info['year'];
        $month = $startdate_info['mon'];
        $day = $startdate_info['mday'];
        $wday = $startdate_info['wday'] - 1;
        while ($max_timestamp > $ts = mktime(0, 0, 0, $month, $day - $wday, $year)) {
            if ($ts > $min_timestamp) {
                $result[] = $ts;
            }
            $day += 7;
        }

        return $result;
    }

    /**
     * Get all half day timestamps in the given range
     *
     * @param int $min_timestamp
     * @param int $max_timestamp
     * @return array
     */
    private function getHalfdayTimestamps($min_timestamp, $max_timestamp)
    {
        $result = array();
        $startdate_info = getdate($min_timestamp);
        $year = $startdate_info['year'];
        $month = $startdate_info['mon'];
        $day = $startdate_info['mday'];
        $hour = 0;
        while ($max_timestamp > $ts = mktime($hour, 0, 0, $month, $day, $year)) {
            if ($ts > $min_timestamp) {
                $result[] = $ts;
            }
            $hour += 12;
        }

        return $result;
    }

    /**
     * Get all full day timestamps in the given range
     *
     * @param int $min_timestamp
     * @param int $max_timestamp
     * @return array
     */
    private function getDayTimestamps($min_timestamp, $max_timestamp)
    {
        $result = array();
        $startdate_info = getdate($min_timestamp);
        $year = $startdate_info['year'];
        $month = $startdate_info['mon'];
        $day = $startdate_info['mday'];
        while ($max_timestamp > $ts = mktime(0, 0, 0, $month, $day, $year)) {
            if ($ts > $min_timestamp) {
                $result[] = $ts;
            }
            $day++;
        }

        return $result;
    }

    /**
     * Get all quarter hour timestamps in the given range
     *
     * @param int $min_timestamp
     * @param int $max_timestamp
     * @return array
     */
    private function getQuarterTimestamps($min_timestamp, $max_timestamp)
    {
        $result = array();
        $startdate_info = getdate($min_timestamp);
        $year = $startdate_info['year'];
        $month = $startdate_info['mon'];
        $day = $startdate_info['mday'];
        $hour = $startdate_info['hours'];
        $minute = 0;
        while ($max_timestamp > $ts = mktime($hour, $minute, 0, $month, $day, $year)) {
            if ($ts > $min_timestamp) {
                $result[] = $ts;
            }
            $minute += 15;
        }

        return $result;
    }

    /**
     * Get all full hour timestamps in the given range
     *
     * @param int $min_timestamp
     * @param int $max_timestamp
     * @return array
     */
    private function getHourTimestamps($min_timestamp, $max_timestamp)
    {
        $result = array();
        $startdate_info = getdate($min_timestamp);
        $year = $startdate_info['year'];
        $month = $startdate_info['mon'];
        $day = $startdate_info['mday'];
        $hour = $startdate_info['hours'];
        while ($max_timestamp > $ts = mktime($hour, 0, 0, $month, $day, $year)) {
            if ($ts > $min_timestamp) {
                $result[] = $ts;
            }
            $hour++;
        }

        return $result;
    }

    /**
     * Get an color RBG Value for the given key
     *
     * @param string $key
     * @return array RGB integer
     */
    protected function getColorRgbByKey($key = '')
    {
        switch (strtoupper($key)) {
            case 'ERROR':
                return array(255, 0, 0);
            case 'WARNING':
                return array(255, 255, 0);
            case 'OK':
                return array(0, 255, 0);
            case 'DUE':
                return array(238, 130, 238);
            case 'ACK':
                return array(0, 0, 255);
            case 'UNDEFINED':
            default:
                return array(100, 100, 100);
        }
    }
}
