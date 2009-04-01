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

	
	function render ($test_result_range, $file ){
		
		if ($test_result_range->getLength() == 0)return;

			// Standard inclusions     
		include("../lib/pChart/class.pData.php");  
		include("../lib/pChart/class.pChart.php");  
		   
			// Dataset definition
		$DataSet = new pData;
		
		foreach ($test_result_range as $result){
			$DataSet->AddPoint($result->getValue(),"Times");  
		    $DataSet->AddPoint($result->getTstamp(),"Values");
		    switch ( $result->getState() ){
		    	case 0:
		    		$DataSet->AddPoint($result->getValue(),"Times_OK");  
		    		$DataSet->AddPoint($result->getTstamp(),"Values_OK");
		    		break;
		    	case 1:
		    		$DataSet->AddPoint($result->getValue(),"Times_WARNING");  
		    		$DataSet->AddPoint($result->getTstamp(),"Times_WARNING");
		    		break;
		    	case 2:
		    		$DataSet->AddPoint($result->getValue(),"Times_ERROR");  
		    		$DataSet->AddPoint($result->getTstamp(),"Values_ERROR");
		    		break;		
		    }
		}

		$DataSet->AddSerie("Times");  
		$DataSet->AddSerie("Values");

			// Set Serie as abcisse label  
		$DataSet->SetAbsciseLabelSerie("Times");  

		$DataSet->SetSerieName("Raw #1","Values");  
		$DataSet->SetSerieName("Raw #2","Times");  
		
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
		$Graph->setColorPalette(10,0,255,0);
		$Graph->setColorPalette(11,255,255,0);
		$Graph->setColorPalette(12,255,0,0);
		  
		$Graph->drawFilledRoundedRectangle(7,7,$width-7,$height-7,5,240,240,240);     
		$Graph->drawRoundedRectangle(5,5,$width-5,$height-5,5,230,230,230);     

		$Graph->setGraphArea(70,30,$width-100,$height-100);  
		$Graph->drawGraphArea(255,255,255,TRUE);  
		
		//$Test->drawGrid(9,TRUE,230,230,230,50);  

		// $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2,TRUE);
		
   		$Graph->setFixedScale(
   			0,
   			$test_result_range->getMaxValue(),
   			$Divisions=5,
   			$test_result_range->getMinTstamp(),
   			$test_result_range->getMaxTstamp(),
   			$XDivisions=5
   		);
   		
			// plot value line
		$Graph->setLineStyle(1,1);
		$Graph->drawXYScale($DataSet->GetData(),$DataSet->GetDataDescription(),"Times","Values",0,0,0,TRUE,45);  
		$Graph->drawXYGraph($DataSet->GetData(),$DataSet->GetDataDescription(),"Times","Values",9);  
		
		// $Graph->drawFilledXYGraph($DataSet->GetData(),$DataSet->GetDataDescription(),"Times","Values",9);  
				
			// plot states
		$DataSet->RemoveSerie("Times");  
		$DataSet->RemoveSerie("Values");
		$DataSet->AddSerie("Times_OK");  
		$DataSet->AddSerie("Values_OK");
		$Graph->drawXYPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),"Times_OK","Values_OK",10,1,1);  

			// plot states
		$DataSet->RemoveSerie("Times");  
		$DataSet->RemoveSerie("Values");
		$DataSet->AddSerie("Times_WARNING");  
		$DataSet->AddSerie("Values_WARNING");
		$Graph->drawXYPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),"Times_WARNING","Values_WARNING",11,2,1);  
		
			// plot states
		$DataSet->RemoveSerie("Times");  
		$DataSet->RemoveSerie("Values");
		$DataSet->AddSerie("Times_ERROR");  
		$DataSet->AddSerie("Values_ERROR");
		$Graph->drawXYPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),"Times_ERROR","Values_ERROR",12,2,1);  
		
		  // Finish the graph#
		/*  
		$Graph->setFontProperties("../lib/Fonts/tahoma.ttf",9);  
		$Graph->drawLegend(600,30,$DataSet->GetDataDescription(),255,255,255);  
		$Graph->setFontProperties("../lib/Fonts/tahoma.ttf",9);  
		$Graph->drawTitle(50,22,"Example 18",50,50,50,585);  
		*/
		$Graph->Render($file);
	}	  
}
?>