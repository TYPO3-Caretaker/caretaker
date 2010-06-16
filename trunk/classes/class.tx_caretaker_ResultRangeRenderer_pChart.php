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

require_once(t3lib_extMgm::extPath('caretaker').'/lib/pChart/class.pData.php');
require_once(t3lib_extMgm::extPath('caretaker').'/lib/pChart/class.pChart.php');

/**
 * Renderer class to generate graphs from caretaker nodeResultRanges.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_ResultRangeRenderer_pChart implements tx_caretaker_ResultRangeRenderer {
	
	private static $instance = null;
	private $width  = 800;
	private $height = 400;
	private $llArray = array();
	private $llKey;
	
	private function __construct ($llArray, $llKey){
		
		$this->llArray = $llArray;
		$this->llKey = $llKey;
	}

	public function getInstance($llArray = array(), $llKey = 'default'){
		if (!self::$instance) {
			self::$instance = new tx_caretaker_ResultRangeRenderer_pChart($llArray, $llKey);
		}
		return self::$instance;
	}

	function setSize ($width, $height){
		$this->width = $width;
		$this->height = $height;
	}
	
	private function getLL($key) {
		$lang = $this->llKey;
		return (!empty($this->llArray[$lang][$key])) ? $this->llArray[$lang][$key] : ((!empty($this->llArray['default'][$key])) ? $this->llArray['default'][$key] : ''); 
	}
	
	/**
	 * (non-PHPdoc)
	 * @see caretaker/trunk/interfaces/tx_caretaker_ResultRangeRenderer#renderTestResultRange()
	 */
	public function renderTestResultRange ($filename, $test_result_range, $title , $value_description ){
		
		if ( ! $test_result_range->getLength() )return false;
		   
			// Dataset definition
		$DataSet   = new pData;
		
		$lastState = tx_caretaker_Constants::state_ok;
		$lastState= -2;
		
		$rangesUndefined = array();
		$rangesOk		 = array(array(0)); // adds the zero to start the ok range at the left border,
											// this is only used if there is no other state at the
											// beginning
		$rangesWarning   = array();
		$rangesError     = array();
		
		$lastValue = false;
		
		foreach ($test_result_range as $result){
			$DataSet->AddPoint($result->getValue(),"Values");  
		    $DataSet->AddPoint($result->getTstamp(),"Times");
		    
		    $state     = $result->getState();
		    
		    if ($state != $lastState){
		    	switch ( $lastState ){
		    		case -1:
			    		$rangesUndefined[count($rangesUndefined)-1][1]= $result->getTstamp();
			    		break;
		    		case 0:
		    			// array count must not be negative
		    			$rangesOk[count($rangesOk)-1 > -1 ? count($rangesOk)-1 : 0][1]= $result->getTstamp();
		    			break;
			    	case 1:
			    		$rangesWarning[count($rangesWarning)-1][1]= $result->getTstamp();
			    		break;
			    	case 2:
			    		$rangesError[count($rangesError)-1][1]= $result->getTstamp();
			    		break;
		    	}
		    	
		    	switch ( $state ){
		    		case -1:
			    		$rangesUndefined[]= Array($result->getTstamp());
			    		break;
			    	case 0:
		    			$rangesOk[] = Array($result->getTstamp());
		    			break;
			    	case 1:
			    		$rangesWarning[]= Array($result->getTstamp());
			    		break;
			    	case 2:
			    		$rangesError[]= Array($result->getTstamp());
			    		break;		
		    	}
		    }
		    $lastState  = $result->getState();
		    $lastResult = $result;
		}

		if ($lastResult) {
			
			switch ( $lastResult->getState() ){
				case -1:
		    		$rangesUndefined[count($rangesUndefined)-1][1]= $lastResult->getTstamp();
		    		break;
				case 0:
					$rangesOk[count($rangesOk) - 1][1] = $lastResult->getTstamp();
					break;
		    	case 1:
		    		$rangesWarning[count($rangesWarning)-1][1]= $lastResult->getTstamp();
		    		break;
		    	case 2:
		    		$rangesError[count($rangesError)-1][1]= $lastResult->getTstamp();
		    		break;		
	    	}
		}

		$value_description = tx_caretaker_LocallizationHelper::locallizeString($value_description);
	
		$DataSet->AddSerie($this->getLL('times'));  
		$DataSet->AddSerie($this->getLL('values'));

		// $DataSet->SetYAxisName($this->getLL('value').( $value_description ?' ['.$value_description.']':''));
		$DataSet->SetYAxisName("");
		// $DataSet->SetXAxisName($this->getLL('date'));
		$DataSet->SetXAxisName("");

		$DataSet->SetAbsciseLabelSerie($this->getLL('values'));  
		$DataSet->SetYAxisFormat("none");
		$DataSet->SetXAxisFormat("none");
		
			// Initialise the graph  
		$width  = $this->width;
		$height = $this->height;
		
		$Graph = new pChart($width,$height);  
		$Graph->setFontProperties(t3lib_extMgm::extPath('caretaker').'/lib/Fonts/tahoma.ttf',9);  
					
			// initialize custom colors
		$Graph->setColorPalette(999,0,0,0);
		$Graph->setColorPalette(0,178,255,178);  //OK
		$Graph->setColorPalette(1,255,255,178); // WARNING
		$Graph->setColorPalette(2,255,178,178); // ERROR
		$Graph->setColorPalette(3,83,83,255); // GRAPH
		
		$Graph->setColorPalette(998,50,50,255); // Graph
		
		$Graph->drawFilledRoundedRectangle(7,7,$width-7,$height-7,5,240,240,240);     
		$Graph->drawRoundedRectangle(5,5,$width-5,$height-5,5,230,230,230);     

		$Graph->setGraphArea(70,30,$width-150,$height-100);  
		$Graph->drawGraphArea(255,255,255,TRUE);  

		$Graph->setFixedScale(
			0,
			$test_result_range->getMaxValue()*1.05,
			5,
			$test_result_range->getMinTstamp(),
			$test_result_range->getMaxTstamp(),
			5
		);

		$Graph->DivisionRatio  = ( $Graph->GArea_Y2 - $Graph->GArea_Y1 ) / ( $test_result_range->getMaxValue()*1.05) ;
		$Graph->XDivisionRatio = ( $Graph->GArea_X2 - $Graph->GArea_X1 ) / ( $test_result_range->getMaxTstamp() - $test_result_range->getMinTstamp() ) ;

			// plot value line
		$DataSet->removeAllSeries();
		$DataSet->AddSerie("Times");
		$DataSet->AddSerie("Values");
		
		$Graph->setLineStyle(1,0);

		
		// $Graph->drawXYGraph($DataSet->GetData(),$DataSet->GetDataDescription(),"Times","Values",13);  
		// $Graph->drawOrthoXYGraph($DataSet->GetData(),$DataSet->GetDataDescription(),"Values","Times",998,50, FALSE);
		$Graph->drawOrthoXYGraph($DataSet->GetData(),$DataSet->GetDataDescription(),"Values","Times",998);  


			// show state as background-color
		foreach($rangesOk as $range){
			if (isset($range[0]) && isset($range[1]) ) {
				$X1 = $Graph->GArea_X1 + (($range[0]-$Graph->VXMin) * $Graph->XDivisionRatio);
				$X2 = $Graph->GArea_X1 + (($range[1]-$Graph->VXMin) * $Graph->XDivisionRatio);
				$Y1 = $Graph->GArea_Y1;
				$Y2 = $Graph->GArea_Y2;
				$Graph->drawFilledRectangle($X1,$Y1,$X2,$Y2,0,255,0,$DrawBorder=FALSE,$Alpha=30,$NoFallBack=FALSE);
			}
		}
		
		foreach($rangesWarning as $range){
			if (isset($range[0]) && isset($range[1]) ) {
				$X1 = $Graph->GArea_X1 + (($range[0]-$Graph->VXMin) * $Graph->XDivisionRatio);
				$X2 = $Graph->GArea_X1 + (($range[1]-$Graph->VXMin) * $Graph->XDivisionRatio);
				$Y1 = $Graph->GArea_Y1;
				$Y2 = $Graph->GArea_Y2;
				$Graph->drawFilledRectangle($X1,$Y1,$X2,$Y2,255,255,0,$DrawBorder=FALSE,$Alpha=30,$NoFallBack=FALSE);
			}
		}
		
		foreach($rangesError as $range){
			if (isset($range[0]) && isset($range[1]) ) {
				$X1 = $Graph->GArea_X1 + (($range[0]-$Graph->VXMin) * $Graph->XDivisionRatio);
				$X2 = $Graph->GArea_X1 + (($range[1]-$Graph->VXMin) * $Graph->XDivisionRatio);
				$Y1 = $Graph->GArea_Y1;
				$Y2 = $Graph->GArea_Y2;
				$Graph->drawFilledRectangle($X1,$Y1,$X2,$Y2,255,0,0,$DrawBorder=FALSE,$Alpha=30,$NoFallBack=FALSE);
			}
		}

			// draw background lines
		$this->drawXAxis($Graph, $test_result_range->getMinTstamp(),  $test_result_range->getMaxTstamp() );
		$this->drawYAxis($Graph, 0,  $test_result_range->getMaxValue() );


			// Finish the graph
		$info = $test_result_range->getInfos();

			// Title
		$Graph->drawTitle(50,22, $title.' '.round(($info['PercentAVAILABLE']*100),2 )."% ".$this->getLL('available'),50,50,50,585);  

			// Legend
		$DataSet->SetSerieName(
			round(($info['PercentOK']*100),2 ).'% '.tx_caretaker_LocallizationHelper::locallizeString('LLL:EXT:caretaker/locallang_fe.xml:state_ok')
			,"Values_OK"
		);
		
		$DataSet->SetSerieName(
			round(($info['PercentWARNING']*100),2 ).'% '.tx_caretaker_LocallizationHelper::locallizeString('LLL:EXT:caretaker/locallang_fe.xml:state_warning')
			,"Values_WARNING"
		);
		
		$DataSet->SetSerieName(
			round(($info['PercentERROR']*100),2 ).'% '.tx_caretaker_LocallizationHelper::locallizeString('LLL:EXT:caretaker/locallang_fe.xml:state_error')
			,"Values_ERROR"
		);
		
		// draw average and median 
		$median  = $test_result_range->getMedianValue();
		$average = $test_result_range->getAverageValue();
		if ($median > 0 || $average > 0 ) {
			$DataSet->SetSerieName(
				'Median: ' . number_format( $median , 2)  
				,"Value_Median"
			);
			$DataSet->SetSerieName(
				'Average:' . number_format( $average,2 ) 
				,"Value_Average"
			);
		}
		
		$Graph->drawLegend($width-140,30,$DataSet->GetDataDescription(),255,255,255);
		$Graph->Render($filename);
		
		return ($filename);
	}	 



	/**
	 * (non-PHPdoc)
	 * @see caretaker/trunk/interfaces/tx_caretaker_ResultRangeRenderer#renderMultipleTestResultRanges()
	 */
	public function renderMultipleTestResultRanges ( $filename, $test_result_ranges, $titles){
		  	
			// Initialise the graph  
		$width  = $this->width;
		$height = $this->height;
		
		$Graph = new pChart($width,$height);  
		$Graph->setFontProperties(t3lib_extMgm::extPath('caretaker').'/lib/Fonts/tahoma.ttf',9);  
					
			// initialize custom colors
		$Graph->setColorPalette(999,0,0,0);
		
		$Graph->drawFilledRoundedRectangle(7,7,$width-7,$height-7,5,240,240,240);     
		$Graph->drawRoundedRectangle(5,5,$width-5,$height-5,5,230,230,230);     

		$Graph->setGraphArea(70,30,$width-250,$height-100);  
		$Graph->drawGraphArea(255,255,255,TRUE);  

		$DataSets = array();
		
		$DataSet  = new pData;
		$DataSet->SetYAxisName("");  
		$DataSet->SetXAxisName("");  
		$DataSet->SetAbsciseLabelSerie("Values");  
		$DataSet->SetYAxisFormat("none");
		$DataSet->SetXAxisFormat("none");

		
		$min_ts  = NULL;
		$max_ts  = NULL;
		$max_val = NULL;
		
		foreach ($test_result_ranges as $key=>$test_result_range){

			$DataSets[$key]   = new pData;
			foreach ($test_result_range as $result){
				$DataSets[$key]->AddPoint((float)$result->getValue(),"Values");  
			    $DataSets[$key]->AddPoint((int)$result->getTstamp(),"Times");
			}
			
			$DataSets[$key]->SetYAxisName($this->getLL('value'));  
			$DataSets[$key]->SetXAxisName($this->getLL('date'));  
			$DataSets[$key]->SetAbsciseLabelSerie("Values");  
			$DataSets[$key]->SetYAxisFormat("none");
			$DataSets[$key]->SetXAxisFormat("none");
			$DataSets[$key]->AddSerie("Values");
			$DataSets[$key]->AddSerie("Times");

			if ($min_ts  == NULL || $min_ts > $test_result_range->getMinTstamp() )$min_ts = $test_result_range->getMinTstamp();
			if ($max_ts  == NULL || $max_ts < $test_result_range->getMaxTstamp() )$max_ts = $test_result_range->getMaxTstamp();
			if ($max_val == NULL || $max_val < $test_result_range->getMaxValue() ) $max_val = $test_result_range->getMaxValue();
			
			$DataSet->AddPoint(0,"Legend_".$key);  
			$DataSet->AddSerie("Legend_".$key);
			if (is_array($titles) && $titles[$key] ){
				$DataSet->SetSerieName(	$titles[$key] ,"Legend_".$key );
			} else {
				$DataSet->SetSerieName(	$key ,"Legend_".$key );
			}
		}
		
		$Graph->setFixedScale(
			0,
			$max_val*1.05,
			5,
			$min_ts,
			$max_ts,
			5
		);

		$Graph->DivisionRatio  = ( $Graph->GArea_Y2 - $Graph->GArea_Y1 ) / ( $max_val*1.05 ) ;
		$Graph->XDivisionRatio = ( $Graph->GArea_X2 - $Graph->GArea_X1 ) / ( $max_ts - $min_ts ) ;
		
		
		$scale_is_plotted = false;
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
		foreach ( $DataSets as $key=>$LocalDataSet ){
				// generate color
			$chartColor = $chartColors[ $key % count($chartColors) ];
			$Graph->setColorPalette($key, $chartColor[0] ,  $chartColor[1],  $chartColor[2] );  //OK
				// plot value line
			$Graph->setLineStyle(1,0);
			$Graph->drawOrthoXYGraph($LocalDataSet->GetData(),$LocalDataSet->GetDataDescription(),"Values","Times",$key);  
		}

		// draw background lines
		$this->drawXAxis($Graph, $min_ts,  $max_ts);
		$this->drawYAxis($Graph, 0,  $max_val);

		
		$Graph->drawTitle(50,22, $description,50,50,50,585);  		
		$Graph->drawLegend($width-240,30,$DataSet->GetDataDescription(),255,255,255);  
		
			// $Graph->drawLegend($width-140,30,$DataSet->GetDataDescription(),255,255,255);  
		$Graph->Render($filename);
		
		return ($filename);
		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see caretaker/trunk/interfaces/tx_caretaker_ResultRangeRenderer#renderAggregatorResultRange()
	 */
	public function renderAggregatorResultRange ($filename, $test_result_range, $title ){

		if ( ! $test_result_range->getLength() ) return false;
				   
			// Dataset definition
		$DataSet   = new pData;

		foreach ($test_result_range as $result){
			
			$undefined = $result->getNumUNDEFINED();  
			$ok        = $result->getNumOK();  
			$warning   = $result->getNumWARNING();
			$error     = $result->getNumERROR(); 

			if ( $max_value < ( $max = ($undefined + $ok + $warning + $error) ) ){
				$max_value = $max;
			} 
				// save Datasets
			$DataSet->AddPoint($ok,"Values_OK");
			$DataSet->AddPoint($ok+$warning,"Values_WARNING");  
			$DataSet->AddPoint($ok+$warning+$error,"Values_ERROR");  
			$DataSet->AddPoint($ok+$warning+$error+$undefined,"Values_UNDEFINED");
			
		    $DataSet->AddPoint($result->getTstamp(),"Times");
		    
		}

		$DataSet->AddSerie("Times");  
		$DataSet->AddSerie("Values_OK");
		$DataSet->AddSerie("Values_WARNING");
		$DataSet->AddSerie("Values_ERROR");
		$DataSet->AddSerie("Values_UNDEFINED");

		//$DataSet->SetYAxisName($this->getLL('value').($value?' ['.$value.']':''));
		//$DataSet->SetXAxisName($this->getLL('date'));
		$DataSet->SetYAxisName("");
		$DataSet->SetXAxisName("");

		$DataSet->SetAbsciseLabelSerie("Times");  
		$DataSet->SetYAxisFormat("none");
		$DataSet->SetXAxisFormat("none");
		
			// Initialise the graph  
		$width  = $this->width;
		$height = $this->height;
		
		$Graph = new pChart($width,$height);  
		$Graph->setFontProperties(t3lib_extMgm::extPath('caretaker').'/lib/Fonts/tahoma.ttf',9);  
					
			// initialize custom colors
		$Graph->setColorPalette(999,0,0,0);
		
		$Graph->setColorPalette(0,0,255,0);   //OK
		$Graph->setColorPalette(1,255,255,0); // WARNING
		$Graph->setColorPalette(2,255,0,0);   // ERROR
		$Graph->setColorPalette(3,200,200,200);  // Undefined	
		
		$Graph->drawFilledRoundedRectangle(7,7,$width-7,$height-7,5,240,240,240);     
		$Graph->drawRoundedRectangle(5,5,$width-5,$height-5,5,230,230,230);     

		$Graph->setGraphArea(70,30,$width-150,$height-100);  
		$Graph->drawGraphArea(255,255,255,TRUE);  

		$Graph->setFixedScale(
			0,
			$max_value + 1,
			$Divisions=5,
			$test_result_range->getMinTstamp(),
			$test_result_range->getMaxTstamp(),
			$XDivisions=5
		);

		$Graph->DivisionRatio  = ( $Graph->GArea_Y2 - $Graph->GArea_Y1 ) / ( $max_value + 1 ) ;
		$Graph->XDivisionRatio = ( $Graph->GArea_X2 - $Graph->GArea_X1 ) / ( $test_result_range->getMaxTstamp()  - $test_result_range->getMinTstamp() ) ;

			// plot value line
		$Graph->setLineStyle(0,0);
		
		$DataSet->removeAllSeries();
		$DataSet->AddSerie("Times");
		$DataSet->AddSerie("Values");
		
		$Graph->setLineStyle(0,0);

		
		$Graph->drawFilledOrthoXYGraph($DataSet->GetData(),$DataSet->GetDataDescription(),"Values_UNDEFINED", "Times" ,3,70, FALSE);
		$Graph->drawFilledOrthoXYGraph($DataSet->GetData(),$DataSet->GetDataDescription(),"Values_ERROR",     "Times" ,2,70, FALSE);
		$Graph->drawFilledOrthoXYGraph($DataSet->GetData(),$DataSet->GetDataDescription(),"Values_WARNING",   "Times" ,1,70, FALSE);
		$Graph->drawFilledOrthoXYGraph($DataSet->GetData(),$DataSet->GetDataDescription(),"Values_OK",        "Times" ,0,70, FALSE);

			// draw background lines
		$this->drawXAxis($Graph, $test_result_range->getMinTstamp(),  $test_result_range->getMaxTstamp() );
		$this->drawYAxis($Graph, 0, $max_value );
		
			// Finish the graph
		$info = $test_result_range->getInfos();
		$Graph->drawTitle(50,22, $title.' '.round(($info['PercentAVAILABLE']*100),2 )."% ".$this->getLL('available'),50,50,50,585);  

			// Legend
		$DataSet->SetSerieName(
			round(($info['PercentOK']*100),2 ).'% '.tx_caretaker_LocallizationHelper::locallizeString('LLL:EXT:caretaker/locallang_fe.xml:state_ok')
			,"Values_OK"
		);

		$DataSet->SetSerieName(
			round(($info['PercentWARNING']*100),2 ).'% '.tx_caretaker_LocallizationHelper::locallizeString('LLL:EXT:caretaker/locallang_fe.xml:state_warning')
			,"Values_WARNING"
		);

		$DataSet->SetSerieName(
			round(($info['PercentERROR']*100),2 ).'% '.tx_caretaker_LocallizationHelper::locallizeString('LLL:EXT:caretaker/locallang_fe.xml:state_error')
			,"Values_ERROR"
		);

		$DataSet->SetSerieName(
			round(($info['PercentUNDEFINED']*100),2 ).'% '.tx_caretaker_LocallizationHelper::locallizeString('LLL:EXT:caretaker/locallang_fe.xml:state_undefined')
			,"Values_UNDEFINED"
		);


				
		$Graph->drawLegend($width-140,30,$DataSet->GetDataDescription(),255,255,255);  
		
		$Graph->Render($filename);
		
		return ($filename);
	}


	/**
	 * Draw the X-Axis Visualisation and Value Information
	 * 
	 * @param <type> $Graph
	 * @param <type> $min_value
	 * @param <type> $max_value 
	 */
	private function drawYAxis(&$Graph, $min_value, $max_value){
		$rounded_value = $this->ceilDecimal($max_value);
		if ($rounded_value > $max_value * 5){
			$value_step        = $rounded_value/40;
		} else if ($rounded_value > $max_value * 2){
			$value_step        = $rounded_value/20;
		} else {
			$value_step        = $rounded_value/10;
		}


		for ($value = 0; $value <= $max_value; $value += $value_step){
				// line
			$YPos = $Graph->GArea_Y2 - (($value-$Graph->VMin) * $Graph->DivisionRatio);
			$Graph->drawFilledRectangle(
				$Graph->GArea_X1 - 3,
				$YPos,
				$Graph->GArea_X2,
				$YPos,
				0,0,0,$DrawBorder=FALSE,$Alpha=15,$NoFallBack=FALSE
			);

				// text
			$font  = t3lib_extMgm::extPath('caretaker').'/lib/Fonts/tahoma.ttf';
			$size  = 9;
			$angle = 0;
			$color = imagecolorallocate  ( $Graph->Picture  , 0  ,0  ,0  );

			$Position   = imageftbbox($size,$angle,$font,$value);
			$TextWidth  = abs($Position[2])+abs($Position[0]);
			$TextHeight = abs($Position[1])+abs($Position[3]);

			$XPos = $Graph->GArea_X1 - $TextWidth - 6;
			imagettftext($Graph->Picture,$size,$angle,floor($XPos)-floor($TextWidth/2),$YPos + 6,$color,$font,$value);
			
		}

		$Graph->drawFilledRectangle(
			$Graph->GArea_X1,
			$Graph->GArea_Y1,
			$Graph->GArea_X1,
			$Graph->GArea_Y2,
			0,0,0,$DrawBorder=FALSE,$Alpha=100,$NoFallBack=FALSE
		);


		
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

	/**
	 * Draw the X-Axis Visualisation and Value Information
	 *
	 * @param <type> $Graph
	 * @param <type> $max_timstamp
	 * @param <type> $min_timestamp
	 */
	private function drawXAxis(&$Graph, $min_timestamp, $max_timstamp){
		$timerange = $max_timstamp - $min_timestamp;

		$times_super = array();
		$times_major = array();
		$times_minor = array();

		$format = '%x';

		if  ( $timerange >= 24*60*60*30*6 ){

			$times_super = $this->getYearTimestamps($min_timestamp,$max_timstamp );
			$times_major = $this->getMonthTimestamps($min_timestamp,$max_timstamp );
			//$times_minor = $this->getWeekTimestamps($min_timestamp,$max_timstamp );
		}
		// 1 Month
		else if  ( $timerange >= 24*60*60*33 ){
			$times_super = $this->getYearTimestamps($min_timestamp,$max_timstamp );
			$times_major = $this->getMonthTimestamps($min_timestamp,$max_timstamp );
			$times_minor = $this->getWeekTimestamps($min_timestamp,$max_timstamp );
		}
		// 7 days
		else if  ( $timerange >= 24*60*60*7 ){
			$format = '%x';
			$times_super = $this->getMonthTimestamps($min_timestamp,$max_timstamp );
			$times_major = $this->getWeekTimestamps($min_timestamp,$max_timstamp );
			$times_minor = $this->getDayTimestamps($min_timestamp,$max_timstamp );
		}
		// 1 day
		else if ( $timerange > 24*60*60 ){
			$format = '%x %H:%M';
			$times_super = $this->getDayTimestamps($min_timestamp,$max_timstamp );
			$times_major = $this->getHalfdayTimestamps($min_timestamp,$max_timstamp );
			$times_minor = $this->getHourTimestamps($min_timestamp,$max_timstamp );
		}
		// < 1 day
		else {
			$format = '%H:%M';
			$times_super = $this->getHalfdayTimestamps($min_timestamp,$max_timstamp );
			$times_major = $this->getHourTimestamps($min_timestamp,$max_timstamp );
			$times_minor = $this->getQuarterTimestamps($min_timestamp,$max_timstamp );
		}

			// draw lines
		foreach ($times_super as $timestamp){
			if ($timestamp > $min_timestamp && $timestamp < $max_timstamp){
				$X = $Graph->GArea_X1 + (($timestamp-$Graph->VXMin) * $Graph->XDivisionRatio);
				$Graph->drawFilledRectangle(
					$X,
					$Graph->GArea_Y1,
					$X+1,
					$Graph->GArea_Y2 + 3,
					0,0,0,$DrawBorder=FALSE,$Alpha=25,$NoFallBack=FALSE
				);
			}
		}

		foreach ($times_major as $timestamp){
			if ($timestamp > $min_timestamp && $timestamp < $max_timstamp){
				$X = $Graph->GArea_X1 + (($timestamp-$Graph->VXMin) * $Graph->XDivisionRatio);
				$Graph->drawFilledRectangle(
					$X,
					$Graph->GArea_Y1,
					$X,
					$Graph->GArea_Y2 + 3,
					0,0,0,$DrawBorder=FALSE,$Alpha=25,$NoFallBack=FALSE
				);
			}
		}

		foreach ($times_minor as $timestamp){
			if ($timestamp > $min_timestamp && $timestamp < $max_timstamp){
				$X = $Graph->GArea_X1 + (($timestamp-$Graph->VXMin) * $Graph->XDivisionRatio);
				$Graph->drawFilledRectangle(
					$X,
					$Graph->GArea_Y1,
					$X,
					$Graph->GArea_Y2 ,
					0,0,0,$DrawBorder=FALSE,$Alpha=15,$NoFallBack=FALSE
				);
			}
		}

		$Graph->drawFilledRectangle(
			$Graph->GArea_X1,
			$Graph->GArea_Y2,
			$Graph->GArea_X2,
			$Graph->GArea_Y2,
			0,0,0,$DrawBorder=FALSE,$Alpha=100,$NoFallBack=FALSE
		);

			// draw x - axis informations
		if (count($times_super)>3){
			$x_axis = $times_super;
		} else if (count($times_major)>3){
			$x_axis = $times_major;
		} else {
			$x_axis = $times_minor;
		}

		foreach ($x_axis as $timestamp){
			$XPos = $Graph->GArea_X1 + (($timestamp-$Graph->VXMin) * $Graph->XDivisionRatio);
			$d = 100;

			$font  = t3lib_extMgm::extPath('caretaker').'/lib/Fonts/tahoma.ttf';
			$size  = 9;
			$angle = 45;
			$info  = strftime($format,$timestamp);
			$color = imagecolorallocate  ( $Graph->Picture  , 0  ,0  ,0  );

			$Position   = imageftbbox($size,$angle,$font,$info);
			$TextWidth  = abs($Position[2])+abs($Position[0]);
			$TextHeight = abs($Position[1])+abs($Position[3]);

			if ( $angle == 0 ) {
				$YPos = $Graph->GArea_Y2+18;
				imagettftext($Graph->Picture,$size,$angle,floor($XPos)-floor($TextWidth/2),$YPos,$color,$font,$info);
			}
			else {
				$YPos = $Graph->GArea_Y2+10+$TextHeight;
				if ( $angle <= 90 )
					imagettftext($Graph->Picture,$size,$angle,floor($XPos)-$TextWidth+5,$YPos,$color,$font,$info);
				else
					imagettftext($Graph->Picture,$size,$angle,floor($XPos)+$TextWidth+5,$YPos,$color,$font,$info);
			}
		}
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