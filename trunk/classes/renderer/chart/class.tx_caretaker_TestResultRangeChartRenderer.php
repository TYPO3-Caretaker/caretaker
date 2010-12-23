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
class tx_caretaker_TestResultRangeChartRenderer extends tx_caretaker_ChartRendererBase {

	var $testResultRange;
	var $testResultRangeInfos;
	var $testResultRangeMedian;
	var $testResultRangeAverage;

	var $colorError;
	var $colorWarning;
	var $colorOk;
	var $colorUndefined;
	var $colorDue;
	var $colorAck;

	public function initChartImage ($image) {

		$this->colorError     = imagecolorallocatealpha($image, 255, 0, 0, 100);
		$this->colorWarning   = imagecolorallocatealpha($image, 255, 255, 0, 100);
		$this->colorOk        = imagecolorallocatealpha($image, 0, 255, 0, 100);
		$this->colorUndefined = imagecolorallocatealpha($image, 100, 100, 100, 100);
		$this->colorDue       = imagecolorallocatealpha($image, 238,130,238, 100);
		$this->colorAck       = imagecolorallocatealpha($image, 0,0,255, 100);
		
	}
	
	/**
	 * 
	 * @param tx_caretaker_TestResultRange $testResultRange 
	 */
	public function setTestResultrange( tx_caretaker_TestResultRange $testResultRange ){
		$this->testResultRange = $testResultRange;
		$this->testResultRangeInfos   = $this->testResultRange->getInfos();
		$this->testResultRangeMedian  = $this->testResultRange->getMedianValue();
		$this->testResultRangeAverage = $this->testResultRange->getAverageValue();

		$this->setStartTimestamp( $this->testResultRange->getStartTimestamp() );
		$this->setEndTimestamp( $this->testResultRange->getEndTimestamp() );

		$this->setMinValue( $this->testResultRange->getMinValue() );
		$this->setMaxValue( $this->testResultRange->getMaxValue() );

		$this->init();
	}

    protected function drawChartImageBackground( &$image ){
		
		$lastX     = NULL;
		$lastState = NULL;

		$count = $this->testResultRange->count();
		$step  = 0;
		foreach ( $this->testResultRange as $key=>$testResult ){
			$step ++;

			$newX = intval( $this->transformX( $testResult->getTimestamp() ) );
			$newState = $testResult->getState();

			if( $lastX !== NULL ){
				switch ( $lastState ){
					case tx_caretaker_Constants::state_ok:
						$color = $this->colorOk;
						break;
					case tx_caretaker_Constants::state_warning:
						$color = $this->colorWarning;
						break;
					case tx_caretaker_Constants::state_error:
						$color = $this->colorError;
						break;
					case tx_caretaker_Constants::state_due:
						$color = $this->colorDue;
						break;
					case tx_caretaker_Constants::state_ack:
						$color = $this->colorAck;
						break;
					default:
						$color = $this->colorUndefined;
						break;
				}
			}
			
			$isLast = ( $step == $count );

			if( $lastX !== NULL && $color && ($newState != $lastState || $isLast ) ){
				imagefilledrectangle( $image, $lastX, $this->marginTop, $newX , $this->height-$this->marginBottom, $color);
			}

			if ($newState !== $lastState){
				$lastX = $newX;
			}
			$lastState = $newState;
			
		}

	}

	protected function drawChartImageForeground( &$image ){
		$lastX = NULL;
		$lastY = NULL;
		$color = imagecolorallocate($image, 0, 0, 255);

		foreach ( $this->testResultRange as $testResult ){
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

	protected function getChartTitle (){
		
		$title = $this->title.' '.round(($this->testResultRangeInfos['PercentAVAILABLE']*100),2 )."% available" ;
		if ( $this->testResultRangeMedian != 0 || $this->testResultRangeAverage != 0 ){
			$title .= ' [Median: ' . number_format( $this->testResultRangeMedian , 2 ) . ', Average: ' . number_format( $this->testResultRangeAverage, 2 ) . ']';
		}
		return $title;		

	}
	
	protected function drawChartImageLegend( &$image, &$chartLegendColor ){
		$legendItems = array(
			
			array($this->colorOk,         $this->testResultRangeInfos['PercentOK'],        'OK'),
			array($this->colorWarning,    $this->testResultRangeInfos['PercentWARNING'],   'Warning' ),
			array($this->colorError,      $this->testResultRangeInfos['PercentERROR'],     'Error' ),
			array($this->colorUndefined,  $this->testResultRangeInfos['PercentUNDEFINED'], 'Undefined'),
			array($this->colorAck,        $this->testResultRangeInfos['PercentACK'],       'ACK'),
			array($this->colorDue,        $this->testResultRangeInfos['PercentDUE'],       'DUE' ),
		);

		$offset = $this->marginTop + 10 ;

		foreach (  $legendItems as $legendItem ){
			
			$x = $this->width - $this->marginRight + 20;
			$y = $offset;

			imagerectangle (  $image ,  $x-5,  $y-8, $x, $y-3, $chartLegendColor);
			imagefilledrectangle( $image , $x-5,  $y-8, $x, $y-3, $legendItem[0] );

			$font  = t3lib_extMgm::extPath('caretaker').'/lib/Fonts/tahoma.ttf';
			$size  = 9;
			$angle = 0;
			imagettftext( $image, $size, $angle, $x + 10, $y, $chartLegendColor, $font, $legendItem[2] );
			
			$offset += 18;
		}
		
		
	}


}
?>
