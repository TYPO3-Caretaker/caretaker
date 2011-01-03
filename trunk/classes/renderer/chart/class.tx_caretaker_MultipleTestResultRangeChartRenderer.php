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
class tx_caretaker_MultipleTestResultRangeChartRenderer extends tx_caretaker_ChartRendererBase {

	/**
	 * Array of tx_caretaker_TestResultRanges
	 * @var array
	 */
	var $testResultRanges = array();

	/**
	 * Array of strings that represent the titles of each range
	 * @var array
	 */
	var $testResultRangeTitles = array();
	
	/**
	 * Add a new test result range
	 * @param tx_caretaker_TestResultRange $testResultRange 
	 */
	public function addTestResultrange( tx_caretaker_TestResultRange $testResultRange, $title ){
		$this->testResultRanges[] = $testResultRange;
		$this->testResultRangeTitles[] = $title;

		if ( !$this->getStartTimestamp() || $this->getStartTimestamp() > $testResultRange->getStartTimestamp() ){
			$this->setStartTimestamp( $testResultRange->getStartTimestamp() );
		}

		if( !$this->getEndTimestamp() || $this->getEndTimestamp() < $testResultRange->getEndTimestamp() ){
			$this->setEndTimestamp( $testResultRange->getEndTimestamp() );

		}

		if( !$this->getMinValue() || $this->getMinValue() > $testResultRange->getMinValue()){
			$this->setMinValue( $testResultRange->getMinValue() );
		}

		if( !$this->getMaxValue() || $this->getMaxValue() < $testResultRange->getMaxValue()){
			$this->setMaxValue( $testResultRange->getMaxValue() );
		}
		
		$this->init();

	}
	
	/**
	 * draw the chart-background into the given chart image
	 * @param resource $image
	 */
    protected function drawChartImageBackground( &$image ){

	}

	/**
	 * draw the chart-foreground into the given chart image
	 * @param resource $image
	 */
	protected function drawChartImageForeground( &$image ){
		

		foreach ( $this->testResultRanges as $key => $testResultRange) {

			$lastX = NULL;
			$lastY = NULL;

			$colorRGB = $this->getChartIndexColor($key);
			$color = imagecolorallocate($image, $colorRGB[0],  $colorRGB[1],  $colorRGB[2]);

			foreach ( $testResultRange as $testResult ){
				$newX = intval( $this->transformX( $testResult->getTimestamp() ) );
				$newY = intval( $this->transformY( $testResult->getValue() ) );
				if( $lastX !== NULL  ){
					imageline ( $image , $lastX, $lastY, $newX, $lastY, $color );
					imageline ( $image , $newX,  $lastY, $newX, $newY,  $color );
				}

				$lastX = $newX;
				$lastY = $newY;
			}
		}
		
	}

	/**
	 * Get the title to display in the chart.
	 * @return string
	 */
	protected function getChartTitle (){
		return '';
	}

	/**
	 * draw the chart-legend into the given chart image
	 * @param resource $image
	 */
	protected function drawChartImageLegend( &$image  ){

		$chartLegendColor = imagecolorallocate($image, 1 , 1 ,1);	
		$offset = $this->marginTop + 10 ;

		foreach ( $this->testResultRanges as $key => $testResultRange ){

			$colorRGB = $this->getChartIndexColor($key);
			$color = imagecolorallocate($image, $colorRGB[0],  $colorRGB[1],  $colorRGB[2]);

			$x = $this->width - $this->marginRight + 20;
			$y = $offset;

			imagefilledrectangle( $image , $x-5,  $y-8, $x, $y-3, $color );
			imagerectangle (  $image ,  $x-5,  $y-8, $x, $y-3, $chartLegendColor);

			$font  = t3lib_extMgm::extPath('caretaker').'/lib/Fonts/tahoma.ttf';
			$size  = 9;
			$angle = 0;
			imagettftext( $image, $size, $angle, $x + 10, $y, $chartLegendColor, $font, $this->testResultRangeTitles[$key] );
			
			$offset += 18;
		}
		
	}

	/**
	 * Get a color for the given index of charts
	 *
	 * @param integer $index
	 * @return array Array with RGB values
	 */
	protected function getChartIndexColor( $index ){
		
		$chartColors = array(
			array( 255 ,   0 ,   0 ),
			array( 255 ,   0 , 255 ),
			array(   0 ,   0 , 255 ),
			array(   0 , 255 , 255 ),
			array(   0 , 255 ,   0 ),
			array( 255 , 255 ,   0 ),
			array( 127 ,   0 ,   0 ),
			array( 127 ,   0 , 127 ),
			array(   0 ,   0 , 127 ),
			array(   0 , 127 , 127 ),
			array(   0 , 127 ,   0 ),
			array( 127 , 127 ,   0 )

		);

		$colorCount = count( $chartColors ) ;
		$colorIndex = ( $index + $colorCount ) % $colorCount ;

		return ( $chartColors[ $colorIndex ] );
		
	}

}
?>
