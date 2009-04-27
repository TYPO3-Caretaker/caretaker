<?php 

require_once(t3lib_extMgm::extPath('caretaker').'/interfaces/interface.tx_caretaker_ResultRangeRenderer.php');
require_once(t3lib_extMgm::extPath('caretaker').'/lib/pChart/class.pData.php');  
require_once(t3lib_extMgm::extPath('caretaker').'/lib/pChart/class.pChart.php');  
		
class tx_caretaker_ResultRangeRenderer_pChart implements tx_caretaker_ResultRangeRenderer {
	
	private static $instance = null;
	private $width  = 700;
	private $height = 400;
	
	private function __construct (){}

	public function getInstance(){
		if (!self::$instance) {
			self::$instance = new tx_caretaker_ResultRangeRenderer_pChart();
		}
		return self::$instance;
	}

	function setSize ($width, $height){
		$this->width = $width;
		$this->height = $height;
	} 
	
	function render ($test_result_range, $file, $value='', $description = '' ){
		if( is_a($test_result_range,'tx_caretaker_TestResultRange') ){
			return $this->render_TestResultRange($test_result_range, $file, $value, $description);
		} else if (is_a($test_result_range,'tx_caretaker_AggregatorResultRange') ){
			return $this->render_AggregatorResultRange($test_result_range, $file, $value, $description);
		}
	}
	
	function render_TestResultRange ($test_result_range, $file, $value='', $description = '' ){
		
		if ($test_result_range->getLength() < 2 )return false;
		   
			// Dataset definition
		$DataSet   = new pData;
		
		$lastState = TX_CARETAKER_STATE_OK;

		$rangesUndefined = array();
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
		    $lastState  = $result->getState();
		    $lastResult = $result;
		}

		if ($lastResult) {
			
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

		$DataSet->SetYAxisName("Value".($value?' ['.$value.']':''));  
		$DataSet->SetXAxisName("Date");  

		$DataSet->SetAbsciseLabelSerie("Values");  
		$DataSet->SetYAxisFormat("number");  
		$DataSet->SetXAxisFormat("date");  
		
			// Initialise the graph  
		$width  = $this->width;
		$height = $this->height;
		
		$Graph = new pChart($width,$height);  
		$Graph->setFontProperties(t3lib_extMgm::extPath('caretaker').'/lib/Fonts/tahoma.ttf',9);  
					
			// initialize custom colors
		$Graph->setColorPalette(999,0,0,0);
		$Graph->setColorPalette(0,0,255,0);  //OK
		$Graph->setColorPalette(1,255,255,0); // WARNING
		$Graph->setColorPalette(2,255,0,0); // ERROR
		$Graph->setColorPalette(3,200,200,200); // Undefined
		
		$Graph->setColorPalette(998,50,50,255); // Graph
		
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
		
		t3lib_div::debug($test_result_range);
		
		// scale drawing changed: added 3 decimal positions
		if(substr($result->getMsg(), 0, 4) == 'PING') {
		
			$Graph->drawXYScale($DataSet->GetData(),$DataSet->GetDataDescription(),"Values","Times",0,0,0,TRUE,45, 3);  
			
		} else {
			
			$Graph->drawXYScale($DataSet->GetData(),$DataSet->GetDataDescription(),"Values","Times",0,0,0,TRUE,45, 0);
		}
		
		$DataSet->removeAllSeries();
		$DataSet->AddSerie("Times");
		$DataSet->AddSerie("Values");
		
		$Graph->setLineStyle(0,0);
		
		// $Graph->drawXYGraph($DataSet->GetData(),$DataSet->GetDataDescription(),"Times","Values",13);  
		$Graph->drawFilledOrthoXYGraph($DataSet->GetData(),$DataSet->GetDataDescription(),"Values","Times",998,50, FALSE);  
		
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
		$info = $test_result_range->getInfos();
		
		$Graph->drawTitle(50,22, $description.' '.round(($info['PercentAVAILABLE']*100),2 )."% Verfügbar",50,50,50,585);  
		
		$DataSet->SetSerieName(
			round(($info['PercentOK']*100),2 ).'% OK'
			,"Values_OK"
		);
		$DataSet->SetSerieName(
			round(($info['PercentWARNING']*100),2 ).'% Warning'
			,"Values_WARNING"
		);
		$DataSet->SetSerieName(
			round(($info['PercentERROR']*100),2 ).'% Error'
			,"Values_ERROR"
		);
		
		$DataSet->SetSerieName(
			round(($info['PercentUNDEFINED']*100),2 ).'% Undefined'
			,"Values_UNDEFINED"
		);
		
		$Graph->drawLegend($width-140,30,$DataSet->GetDataDescription(),255,255,255);  
		$Graph->Render($file);
		
		return ($file);
	}	 
	
	function render_MultiTestResultRanges ( $test_result_ranges, $file, $titles=false, $description = '' ){
		  	
			// Initialise the graph  
		$width  = $this->width;
		$height = $this->height;
		
		$Graph = new pChart($width,$height);  
		$Graph->setFontProperties(t3lib_extMgm::extPath('caretaker').'/lib/Fonts/tahoma.ttf',9);  
					
			// initialize custom colors
		$Graph->setColorPalette(999,0,0,0);
		
		$Graph->drawFilledRoundedRectangle(7,7,$width-7,$height-7,5,240,240,240);     
		$Graph->drawRoundedRectangle(5,5,$width-5,$height-5,5,230,230,230);     

		$Graph->setGraphArea(70,30,$width-150,$height-100);  
		$Graph->drawGraphArea(255,255,255,TRUE);  

		$DataSets = array();
		
		$DataSet  = new pData;
		$DataSet->SetYAxisName("Value");  
		$DataSet->SetXAxisName("Date");  
		$DataSet->SetAbsciseLabelSerie("Values");  
		$DataSet->SetYAxisFormat("number");  
		$DataSet->SetXAxisFormat("date");  

		
		$min_ts  = NULL;
		$max_ts  = NULL;
		$max_val = NULL;
		
		foreach ($test_result_ranges as $key=>$test_result_range){

			$DataSets[$key]   = new pData;
			foreach ($test_result_range as $result){
				$DataSets[$key]->AddPoint((float)$result->getValue(),"Values");  
			    $DataSets[$key]->AddPoint((int)$result->getTstamp(),"Times");
			}
			
			$DataSets[$key]->SetYAxisName("Value");  
			$DataSets[$key]->SetXAxisName("Date");  
			$DataSets[$key]->SetAbsciseLabelSerie("Values");  
			$DataSets[$key]->SetYAxisFormat("number");  
			$DataSets[$key]->SetXAxisFormat("date");  
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
		
		$scale_is_plotted = false;
		foreach ( $DataSets as $key=>$LocalDataSet ){
				// generate color
			$Graph->setColorPalette($key, (($key*100)+0)%255 ,(($key*100)+85)%255, (($key*100)+170)%255);  //OK
				// plot scale once
			if (!$scale_is_plotted){
				$Graph->drawXYScale($LocalDataSet->GetData(),$LocalDataSet->GetDataDescription(),"Times","Values",0,0,0,TRUE,45);
				$scale_is_plotted = true;  
			}
				// plot value line
			$Graph->setLineStyle(0,0);
			$Graph->drawOrthoXYGraph($LocalDataSet->GetData(),$LocalDataSet->GetDataDescription(),"Values","Times",$key);  
		}
		
		$Graph->drawTitle(50,22, $description,50,50,50,585);  		
		$Graph->drawLegend($width-140,30,$DataSet->GetDataDescription(),255,255,255);  
		
			// $Graph->drawLegend($width-140,30,$DataSet->GetDataDescription(),255,255,255);  
		$Graph->Render($file);
		
		return ($file);
		
	}
	
	function render_AggregatorResultRange ($test_result_range, $file, $value='', $description = '' ){

		if ($test_result_range->getLength() <2 )return false;
				   
			// Dataset definition
		$DataSet   = new pData;
		
		$lastState = TX_CARETAKER_STATE_OK;

		$rangesUndefined = array();
		$rangesWarning   = array();
		$rangesError     = array();
		
		$lastValue = false;
		
		foreach ($test_result_range as $result){
			
			$undefined = $result->getNumUNDEFINED();  
			$ok        = $result->getNumOK();  
			$warning   = $result->getNumERROR();  
			$error     = $result->getNumWARNING();  
	
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

		$DataSet->SetYAxisName("Value".($value?' ['.$value.']':''));  
		$DataSet->SetXAxisName("Date");  

		$DataSet->SetAbsciseLabelSerie("Times");  
		$DataSet->SetYAxisFormat("number");  
		$DataSet->SetXAxisFormat("date");  
		
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
			$test_result_range->getMaxValue() + 1,
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
		$Graph->drawFilledOrthoXYGraph($DataSet->GetData(),$DataSet->GetDataDescription(),"Values_UNDEFINED","Times",3,70, FALSE);  
		$Graph->drawFilledOrthoXYGraph($DataSet->GetData(),$DataSet->GetDataDescription(),"Values_ERROR","Times",2,70, FALSE);  
		$Graph->drawFilledOrthoXYGraph($DataSet->GetData(),$DataSet->GetDataDescription(),"Values_WARNING","Times",1,70, FALSE);  
		$Graph->drawFilledOrthoXYGraph($DataSet->GetData(),$DataSet->GetDataDescription(),"Values_OK","Times",0,70, FALSE);  
		
		  // Finish the graph#
		$info = $test_result_range->getInfos();
		$Graph->drawTitle(50,22, $description.' '.round(($info['PercentAVAILABLE']*100),2 )."% Verfügbar",50,50,50,585);  
		
		$DataSet->SetSerieName(
			round(($info['PercentOK']*100),2 ).'% OK'
			,"Values_OK"
		);
		$DataSet->SetSerieName(
			round(($info['PercentWARNING']*100),2 ).'% Warning'
			,"Values_WARNING"
		);
		$DataSet->SetSerieName(
			round(($info['PercentERROR']*100),2 ).'% Error'
			,"Values_ERROR"
		);
		
		$DataSet->SetSerieName(
			round(($info['PercentUNDEFINED']*100),2 ).'% Undefined'
			,"Values_UNDEFINED"
		);
				
		$Graph->drawLegend($width-140,30,$DataSet->GetDataDescription(),255,255,255);  
		
		$Graph->Render($file);
		
		return ($file);
	}	 
}
?>