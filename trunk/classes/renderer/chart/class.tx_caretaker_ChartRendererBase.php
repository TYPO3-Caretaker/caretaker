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
 * Baseclass for all Chart Renderers.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
abstract class tx_caretaker_ChartRendererBase {

	protected $width  = 800;
	protected $height = 400;

	protected $chartWidth;
	protected $chartHeight;

	protected $chartFactorX;
	protected $chartFactorY;

	protected $minValue;
	protected $maxValue;

	protected $startDate;
	protected $stopDate;

	protected $title;

	protected $marginLeft   = 70;
	protected $marginRight  = 150;
	protected $marginTop    = 30;
	protected $marginBottom = 70;


	public function __construct( $width = 800, $height = 400 ){
		$this->width  = $width;
		$this->height = $height;
	}

	protected function setStartTimestamp( $startTimestamp ){
		$this->startTimestamp = $startTimestamp;
	}

	protected function getStartTimestamp (){
		return $this->startTimestamp;
	}
	
	protected function setEndTimestamp ($endTimestamp){
		$this->endTimestamp = $endTimestamp;
	}

	protected function getEndTimestamp (){
		return $this->endTimestamp;
	}

	protected function setMinValue( $minValue ){
		$this->minValue = $minValue;
	}

	protected function getMinValue (){
		return $this->minValue;
	}

	protected function setMaxValue( $maxValue ){
		$this->maxValue = $maxValue;
		if ( $this->maxValue == 0){
			$maxValue ++;
		}
		$this->maxValue = $this->maxValue * 1.05;
	}

	protected function getMaxValue (){
		return $this->maxValue;
	}

	public function setTitle( $title ){
		$this->title = $title;
	}
	
	protected function initChartImage( &$image){
		
	}

	abstract protected function getChartTitle();
	
	abstract protected function drawChartImageBackground( &$image );

	abstract protected function drawChartImageForeground( &$image );


	protected function init(){
		
		// calculate chart Area
		$this->chartWidth  = $this->width  - $this->marginLeft - $this->marginRight ;
		$this->chartHeight = $this->height - $this->marginTop  - $this->marginBottom ;

		// calculate the ranges
		$this->scaleX = $this->chartWidth  / ( $this->endTimestamp - $this->startTimestamp );
		$this->scaleY = $this->chartHeight / ( $this->maxValue );

	 	$this->baseX  = $this->startTimestamp;
		$this->baseY  = 0;
	}

	/**
	 * transform values from valuespace to chart-image space
	 * @param float $value
	 * @return float 
	 */
	protected function transformX ($value){
		return $this->marginLeft + ($value - $this->baseX) * $this->scaleX;
	}

	/**
	 * transform values from valuespace to chart-image space
	 * @param float $value
	 * @return float 
	 */
	protected function transformY ($value){
		return $this->marginTop + $this->chartHeight - ( ( $value - $this->baseY ) * $this->scaleY );
	}
	
	/**
	 * combine the chart images from the given parts
	 */
	public function getChartImage  () {

		// create image of width and height
		$image = imagecreatetruecolor ( $this->width ,  $this->height );

		$this->initChartImage($image);
		
		// Make the background transparent
		$background = imagecolorallocate($image,255,255,255);
		imagefilledrectangle( $image, 0, 0, $this->width ,  $this->height, $background);
		imagecolortransparent( $image ,$background);

		// fill chart area
		$chartBackgroundColor  = imagecolorallocate($image, 254, 254, 254);
		$chartLegendColor      = imagecolorallocate($image, 1, 1, 1);
		$chartLegendColor2     = imagecolorallocatealpha($image, 1, 1, 1, 60 );
		$chartLegendColor3     = imagecolorallocatealpha($image, 1, 1, 1, 90 );


		imagefilledrectangle( $image , $this->marginLeft ,  $this->marginTop ,  $this->width  - $this->marginRight  , $this->height - $this->marginBottom , $chartBackgroundColor );

				
		// combine with chartImageBackground
		$this->drawChartImageBackground( $image );

		// combine with chart axes
		imagerectangle (  $image , $this->marginLeft ,  $this->marginTop ,  $this->width  - $this->marginRight  , $this->height - $this->marginBottom , $chartLegendColor);
		$this->drawYAxis( $image , $chartLegendColor, $chartLegendColor2  );
		$this->drawXAxis( $image , $chartLegendColor, $chartLegendColor2, $chartLegendColor3 );


		// combine with getChartImageForeground
		$this->drawChartImageForeground( $image );
		
		// combine with getChartImageLegend
		$this->drawChartImageLegend( $image, $chartLegendColor );

		
		// combine with getChartImageTitle
		$this->drawChartTitle( $image , $chartLegendColor, $this->getChartTitle() );
		

		return $image;
	}

	public function getChartImagePng( $filename ) {
		$image = $this->getChartImage();
		imagepng ( $image, PATH_site.$filename );
		imagedestroy( $image );
		return ( $filename );
	}

	/**
	 * write the chart image and return the appropriate image tag
	 */
	public function getChartImageTag( $filename, $baseUrl = '' ) {
		$imagePng = $this->getChartImagePng( $filename );
		return '<img  src="' . $baseUrl . $imagePng . '" width="' . $this->width . '" height="' . $this->height . '" />';
	}


	

	private function drawChartTitle( &$image, &$chartLegendColor, $title ){
		$font  = t3lib_extMgm::extPath('caretaker').'/lib/Fonts/tahoma.ttf';
		$size  = 9;
		$angle = 0;

		$Position   = imageftbbox($size,$angle,$font,$title);
		$TextWidth  = abs($Position[2])-abs($Position[0]);
		$TextHeight = abs($Position[1])-abs($Position[3]);

		$color = imagecolorallocate  ($image  ,255 ,1 ,1 );

		imagettftext( $image, $size, $angle, $this->marginLeft + floor($this->chartWidth/2) - $TextWidth / 2, 20, $chartLegendColor, $font, $title);

	}

	/**
	 * Draw the X-Axis Visualisation and Value Information into the chart image
	 *
	 * @param image $Graph
	 */
	private function drawYAxis( &$image , &$chartLegendColor, &$chartLegendColor2 ){

		// detect the y axis separation
		$rounded_value = $this->ceilDecimal($this->maxValue);

		if ($rounded_value > $this->maxValue * 5){
			$value_step        = $rounded_value/40;
		} else if ($rounded_value > $this->maxValue * 2){
			$value_step        = $rounded_value/20;
		} else {
			$value_step        = $rounded_value/10;
		}

		for ($value = 0; $value <= $this->maxValue; $value += $value_step){
			// line
			$scaledValueY = intval( $this->transformY($value) );
			imageline ( $image , $this->marginLeft - 3 , $scaledValueY ,  $this->width - $this->marginRight , $scaledValueY , $chartLegendColor2 );

			// text
			$font  = t3lib_extMgm::extPath('caretaker').'/lib/Fonts/tahoma.ttf';
			$size  = 9;
			$angle = 0;
		
			$Position   = imageftbbox($size,$angle,$font,$value);
			$TextWidth  = abs($Position[2])-abs($Position[0]);
			$TextHeight = abs($Position[1])-abs($Position[3]);

			$color = imagecolorallocate  ($image  ,255 ,1 ,1 );
			
			imagettftext( $image, $size, $angle, $this->marginLeft - 10 - floor( $TextWidth ) , $scaledValueY + 6, $chartLegendColor, $font, $value);
		}
	}

	/**
	 * Draw the X-Axis Visualisation and Value Information
	 *
	 * @param <type> $Graph
	 * @param <type> $max_timstamp
	 * @param <type> $min_timestamp
	 */
	private function drawXAxis( &$image, &$chartLegendColor, &$chartLegendColor2, &$chartLegendColor3 ){
		
		$timerange = $this->endTimestamp - $this->startTimestamp ;

		$times_super = array();
		$times_major = array();
		$times_minor = array();

		$format = '%x';

		// year
		if  ( $timerange >= 24*60*60*30*6 ){
			$times_super = $this->getYearTimestamps($this->startTimestamp,$this->endTimestamp );
			$times_major = $this->getMonthTimestamps($this->startTimestamp,$this->endTimestamp );
			//$times_minor = $this->getWeekTimestamps($this->startTimestamp,$this->endTimestamp );
		}
		// quarter
		if  ( $timerange >= 24*60*60*30*3 ){
			$times_super = $this->getYearTimestamps($this->startTimestamp,$this->endTimestamp );
			$times_major = $this->getMonthTimestamps($this->startTimestamp,$this->endTimestamp );
			$times_minor = $this->getWeekTimestamps($this->startTimestamp,$this->endTimestamp );
		}
		// 1 Month
		else if  ( $timerange >= 24*60*60*30 ){
			$times_super = $this->getMonthTimestamps($this->startTimestamp,$this->endTimestamp );
			$times_major = $this->getWeekTimestamps($this->startTimestamp,$this->endTimestamp );
			$times_minor = $this->getDayTimestamps($this->startTimestamp,$this->endTimestamp );
		}
		// 7 days
		else if  ( $timerange >= 24*60*60*7 ){
			$format = '%x';
			$times_super = $this->getMonthTimestamps($this->startTimestamp,$this->endTimestamp );
			$times_major = $this->getWeekTimestamps($this->startTimestamp,$this->endTimestamp );
			$times_minor = $this->getHalfdayTimestamps($this->startTimestamp,$this->endTimestamp );
		}
		// 1 day
		else if ( $timerange >= 24*60*60*2 ){
			$format = '%x';
			$times_super = $this->getDayTimestamps($this->startTimestamp,$this->endTimestamp );
			$times_major = $this->getHalfdayTimestamps($this->startTimestamp,$this->endTimestamp );
			$times_minor = $this->getHourTimestamps($this->startTimestamp,$this->endTimestamp );
		}
		// 1 day
		else if ( $timerange >= 24*60*60*1 ){
			$format = '%x %H:%M';
			$times_super = $this->getDayTimestamps($this->startTimestamp,$this->endTimestamp );
			$times_major = $this->getHalfdayTimestamps($this->startTimestamp,$this->endTimestamp );
			$times_minor = $this->getHourTimestamps($this->startTimestamp,$this->endTimestamp );
		}
		// < 1 day
		else {
			$format = '%H:%M';
			$times_super = $this->getHalfdayTimestamps($this->startTimestamp,$this->endTimestamp );
			$times_major = $this->getHourTimestamps($this->startTimestamp,$this->endTimestamp );
			$times_minor = $this->getQuarterTimestamps($this->startTimestamp,$this->endTimestamp );
		}

			// draw lines
		foreach ($times_super as $timestamp){
			if ($timestamp > $this->startTimestamp && $timestamp < $this->endTimestamp){
				$scaledTime = $this->transformX( $timestamp );
				imageline ( $image , $scaledTime , $this->marginTop , $scaledTime , $this->height - $this->marginBottom + 3 , $chartLegendColor );
			}
		}

		foreach ($times_major as $timestamp){
			if ($timestamp > $this->startTimestamp && $timestamp < $this->endTimestamp){
				$scaledTime = $this->transformX( $timestamp );
				imageline ( $image , $scaledTime , $this->marginTop , $scaledTime , $this->height - $this->marginBottom + 3 , $chartLegendColor2 );

			}
		}
		
		foreach ($times_minor as $timestamp){
			if ($timestamp > $this->startTimestamp && $timestamp < $this->endTimestamp){
				$scaledTime = $this->transformX( $timestamp );
				imageline ( $image , $scaledTime , $this->marginTop , $scaledTime , $this->height - $this->marginBottom , $chartLegendColor3 );

			}
		}

			// draw x - axis informations
		if (count($times_super)>3){
			$x_axis = $times_super;
		} else if (count($times_major)>3){
			$x_axis = $times_major;
		} else {
			$x_axis = $times_minor;
		}

		foreach ($x_axis as $timestamp){
			$scaledTime = $this->transformX( $timestamp );
			$d = 100;

			$font  = t3lib_extMgm::extPath('caretaker').'/lib/Fonts/tahoma.ttf';
			$size  = 9;
			$angle = 45;
			$info  = strftime($format,$timestamp);

			
			$Position   = imageftbbox($size,$angle,$font,$info);
			$TextWidth  = abs($Position[2])-abs($Position[0]);
			$TextHeight = abs($Position[1])-abs($Position[3]);

			$scaledTime = $this->transformX( $timestamp );

			imagettftext($image,$size,$angle, floor($scaledTime)-$TextWidth, $this->height - $this->marginBottom - $TextHeight + 10 ,$chartLegendColor,$font,$info);
			
		}
	}


	

	private function ceilDecimal($value){

		$number  = (float)$value;

		if ($number >1 || $number < -1){
			$abs_str = (string)round(abs($number));
			$significance = pow(10 , (int)strlen( $abs_str ) );
		} else if ( $value == 0) {
			return 1;
		} else {
			$abs_str = (string)abs($number);
			$pos = 0;
			while ( substr($abs_str,$pos,1) == "0" || substr($abs_str,$pos,1) == "." ){
				$pos ++;
			}
			$significance = pow(10 , 1+$pos*-1);
		}

		if ($number > 0){
			$result = ceil($number/$significance)*$significance;
		} else {
			$result = -1 * ceil(abs($number)/$significance)*$significance;
		}

		return ( $result );
	}

	

	private function getYearTimestamps($min_timestamp, $max_timstamp){
		$result = array();
		$startdate_info = getdate($min_timestamp);
		$year  = $startdate_info['year'];
		while ( $max_timstamp > $ts = mktime( 0, 0 , 0 , 1, 1, $year ) ){
			if ($ts > $min_timestamp) $result[] = $ts;
			$year ++;
		}
		return $result;
	}

	private function getMonthTimestamps($min_timestamp, $max_timstamp){
		$result = array();
		$startdate_info = getdate($min_timestamp);
		$year  = $startdate_info['year'];
		$month = $startdate_info['mon'];
		while ( $max_timstamp > $ts = mktime( 0, 0 , 0 , $month, 1, $year ) ){
			if ($ts > $min_timestamp) $result[] = $ts;
			$month ++;
		}
		return $result;
	}

	private function getWeekTimestamps($min_timestamp, $max_timstamp){
		$result = array();
		$startdate_info = getdate($min_timestamp);
		$year  = $startdate_info['year'];
		$month = $startdate_info['mon'];
		$day   = $startdate_info['mday'];
		$wday  = $startdate_info['wday'] - 1;
		while ( $max_timstamp > $ts = mktime( 0, 0 , 0 , $month, $day-$wday, $year ) ){
			if ($ts > $min_timestamp) $result[] = $ts;
			$day += 7;
		}
		return $result;
	}

	private function getHalfdayTimestamps($min_timestamp, $max_timstamp){
		$result = array();
		$startdate_info = getdate($min_timestamp);
		$year  = $startdate_info['year'];
		$month = $startdate_info['mon'];
		$day   = $startdate_info['mday'];
		$hour  = 0;
		while ( $max_timstamp > $ts = mktime( $hour, 0 , 0 , $month, $day, $year ) ){
			if ($ts > $min_timestamp) $result[] = $ts;
			$hour += 12;
		}
		return $result;
	}

	private function getDayTimestamps($min_timestamp, $max_timstamp){
		$result = array();
		$startdate_info = getdate($min_timestamp);
		$year  = $startdate_info['year'];
		$month = $startdate_info['mon'];
		$day   = $startdate_info['mday'];
		while ( $max_timstamp > $ts = mktime( 0, 0 , 0 , $month, $day, $year ) ){
			if ($ts > $min_timestamp) $result[] = $ts;
			$day ++;
		}
		return $result;
	}

	private function getQuarterTimestamps($min_timestamp, $max_timstamp){
		$result = array();
		$startdate_info = getdate($min_timestamp);
		$year  = $startdate_info['year'];
		$month = $startdate_info['mon'];
		$day   = $startdate_info['mday'];
		$hour  = $startdate_info['hours'];
		$minute = 0;
		while ( $max_timstamp > $ts = mktime( $hour, $minute , 0 , $month, $day, $year ) ){
			if ($ts > $min_timestamp) $result[] = $ts;
			$minute += 15;
		}
		return $result;
	}



	private function getHourTimestamps($min_timestamp, $max_timstamp){
		$result = array();
		$startdate_info = getdate($min_timestamp);
		$year  = $startdate_info['year'];
		$month = $startdate_info['mon'];
		$day   = $startdate_info['mday'];
		$hour  = $startdate_info['hours'];
		while ( $max_timstamp > $ts = mktime( $hour, 0 , 0 , $month, $day, $year ) ){
			if ($ts > $min_timestamp)
				$result[] = $ts;
			$hour ++;
		}
		return $result;
	}

}
?>
