<?php 

require_once('interface.tx_caretaker_TestResultRangeRenderer.php');

class tx_caretaker_TestResultRangeRenderer_pChart implements tx_caretaker_TestResultRangeRenderer {
	
	private static $instance = null;
	
	private function __construct (){}	

	public function getInstance(){
		if (!self::$instance) {
			self::$instance = new tx_caretaker_TestResultRangeRenderer_pChart();
		}
		return self::$instance;
	}

	
	function render ($test_result_range, $file, $description = '' ){
		
		if ($test_result_range->getLength() == 0)return false;

			// Standard inclusions     
		include("../lib/pChart/class.pData.php");  
		include("../lib/pChart/class.pChart.php");  
		   
			// Dataset definition
		$DataSet   = new pData;
		
		$lastState = TX_CARETAKER_STATE_OK;

		$rangesUndefined = array();
		$rangesWarning   = array();
		$rangesError     = array();
		
		$lastValue = false;
		
		foreach ($test_result_range as $result){
			$DataSet->AddPoint($result->getValue(),"Times");  
		    $DataSet->AddPoint($result->getTstamp(),"Values");
		    
		    $state     = $result->getState();
		    
		    if ($state != $lastState){
		    	switch ( $lastState ){
		    		case -1:
			    		$rangesUndefined[count($rangesUndefined)-1][1]= $result->getTstamp();
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
			    	case 1:
			    		$rangesWarning[]= Array($result->getTstamp());
			    		break;
			    	case 2:
			    		$rangesError[]= Array($result->getTstamp());
			    		break;		
		    	}
		    }
		    $lastState  = $result->getValue();
		    $lastResult = $result;
		}

		if ($lastResult) {
			
			// $DataSet->AddPoint($lastValue,"Values");  
			// $DataSet->AddPoint($test_result_range->getMaxTstamp()+1000000,"Times");
			  
			switch ( $lastResult->getState() ){
				case -1:
		    		$rangesUndefined[count($rangesUndefined)-1][1]= $lastResult->getTstamp();
		    		break;	
		    	case 1:
		    		$rangesWarning[count($rangesWarning)-1][1]= $lastResult->getTstamp();
		    		break;
		    	case 2:
		    		$rangesError[count($rangesError)-1][1]= $lastResult->getTstamp();
		    		break;		
	    	}
		}
		
		$DataSet->AddSerie("Times");  
		$DataSet->AddSerie("Values");

		$DataSet->SetYAxisName("Value");  
		$DataSet->SetXAxisName("Date");  

		$DataSet->SetAbsciseLabelSerie("Times");  
		$DataSet->SetYAxisFormat("number");  
		$DataSet->SetXAxisFormat("date");  
		
			// Initialise the graph  
		$width = 700;
		$height = 400;
		
		$Graph = new pChart($width,$height);  
		$Graph->setFontProperties("../lib/Fonts/tahoma.ttf",9);
			
			// initialize custom colors
		$Graph->setColorPalette(9,0,0,0);
		$Graph->setColorPalette(10,0,255,0);  //OK
		$Graph->setColorPalette(11,255,255,0); // WARNING
		$Graph->setColorPalette(12,255,0,0); // ERROR
		$Graph->setColorPalette(14,60,60,60); // Undefined
		$Graph->setColorPalette(13,50,50,255); // Graph
		
		$Graph->drawFilledRoundedRectangle(7,7,$width-7,$height-7,5,240,240,240);     
		$Graph->drawRoundedRectangle(5,5,$width-5,$height-5,5,230,230,230);     

		$Graph->setGraphArea(70,30,$width-150,$height-100);  
		$Graph->drawGraphArea(255,255,255,TRUE);  

		$Graph->setFixedScale(
			0,
			$test_result_range->getMaxValue()*1.05,
			$Divisions=5,
			$test_result_range->getMinTstamp(),
			$test_result_range->getMaxTstamp(),
			$XDivisions=5
		);

			// plot value line
		$Graph->setLineStyle(0,0);
		$Graph->drawXYScale($DataSet->GetData(),$DataSet->GetDataDescription(),"Times","Values",0,0,0,TRUE,45);  
		
		$DataSet->removeAllSeries();
		$DataSet->AddSerie("Times");
		$DataSet->AddSerie("Values");
		
		$Graph->setLineStyle(0,0);
		
		// $Graph->drawXYGraph($DataSet->GetData(),$DataSet->GetDataDescription(),"Times","Values",13);  
		$Graph->drawFilledOrthoXYGraph($DataSet->GetData(),$DataSet->GetDataDescription(),"Times","Values",13,50, FALSE);  

			// mark ranges of values wich are not ok
		foreach($rangesUndefined as $range){
			if (isset($range[0]) && isset($range[1]) ) {
				$X1 = $Graph->GArea_X1 + (($range[0]-$Graph->VXMin) * $Graph->XDivisionRatio);
				$X2 = $Graph->GArea_X1 + (($range[1]-$Graph->VXMin) * $Graph->XDivisionRatio);
				$Y1 = $Graph->GArea_Y1;
				$Y2 = $Graph->GArea_Y2;
				$Graph->drawFilledRectangle($X1,$Y1,$X2,$Y2,0,0,255,$DrawBorder=FALSE,$Alpha=30,$NoFallBack=FALSE);
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
				
		  // Finish the graph#
		$Graph->setFontProperties("../lib/Fonts/tahoma.ttf",9);  
		$Graph->drawTitle(50,22, $description.' '.round(($test_result_range->getAvailability()*100),2 )."% Verfügbar",50,50,50,585);  
		
		
		$DataSet->SetSerieName(
			round(($test_result_range->getPercentUndefined()*100),2 ).'% Undefined'
			,"Values_UNDEFINED"
		);
		$DataSet->SetSerieName(
			round(($test_result_range->getPercentWarning()*100),2 ).'% Warning'
			,"Values_WARNING"
		);
		$DataSet->SetSerieName(
			round(($test_result_range->getPercentError()*100),2 ).'% Error'
			,"Values_ERROR"
		);
		
		
		$Graph->setFontProperties("../lib/Fonts/tahoma.ttf",9);  
		$Graph->drawLegend($width-140,30,$DataSet->GetDataDescription(),255,255,255);  
		
		$Graph->Render($file);
		
		return ($file);
	}	  
}
?>