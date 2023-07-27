<?php

class toggleview{
	
	private $type;
	
	public function __construct($type){
		$this->type = $type;
	}
	
	private function get_table_name(){
		$classname = $this->type;
		return $classname::TABLE;
	}
	
	private function get_pretty_name(){
		$classname = $this->type;
		return $classname::PRETTY_NAME;
	}
	
	public function invoke(){
		
		$output = new buildablecollection();
		
		$defaults = array(filteractive::ACTIVE_ITEMS=>filteractive::ONLY_ACTIVE,
			filtercompanyidtoggle::COMPANY_ID=>$_SESSION['FT']['company_id']);
		
		$orchestrator = new reportorchestrator();
		$orchestrator->add_customizer(new filteractive());
		$orchestrator->add_customizer(new filtercompanyidtoggle(false, true));
		$orchestrator->set_defaults($defaults);
		$orchestrator->process_array($_GET);
		
		$wherebuilder = new reportwherebuilder();
		$frombuilder = new togglefrombuilder($this->get_table_name(), 't');
		$orderbuilder = new reportorderbuilder();
		$orderbuilder->parse_get('name', 'asc');
		
		$orchestrator->combine_wheres($wherebuilder);
		
		$report = new togglereport();
		$report->add_other_vars($orchestrator->get_active_vars());
		$result = $report->execute($wherebuilder, $frombuilder, $orderbuilder);
		
		/*
		$output->add($orchestrator->get_customizer());
		
		$tabletitle = new tabletitle($orchestrator->get_headline($this->get_pretty_name()));
		$output->add($tabletitle);
		*/
		if($result){
		    return $result;
			//$output->add($report->get_table());
		} else {
		    echo 'No results found';
			//$output->add($this->build_error($report->build_error_no_result()));
		}
		
		return $output;
		
	}
	
	private function build_error($msg){
		$buildable = new basicbuildable();
		$buildable->add_content($msg, 'error');
		return $buildable;
	}
}