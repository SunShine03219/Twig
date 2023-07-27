<?php

class companyview{
	
	public function invoke(){
		$output = new buildablecollection();
		
		$defaults = array(filteractive::ACTIVE_ITEMS=>filteractive::ONLY_ACTIVE);
		
		$orchestrator = new reportorchestrator();
		$filteractive = new filteractive();
		$orchestrator->add_customizer($filteractive);
		$orchestrator->set_defaults($defaults);
		$orchestrator->process_array($_GET);
		
		$wherebuilder = new reportwherebuilder();
		$frombuilder = new togglefrombuilder(company::TABLE, 't');
		$orderbuilder = new reportorderbuilder();
		$orderbuilder->parse_get('name', 'asc');
		
		$orchestrator->combine_wheres($wherebuilder);
		if(isset($_GET['name'])){
			$wherebuilder->add_custom("LOWER(name) like LOWER('%". $_GET['name'] ."%')");
		}
		$report = new companyreport();
		$report->add_other_vars($orchestrator->get_active_vars());
		$result = $report->execute($wherebuilder, $frombuilder, $orderbuilder);
		/*
		$output->add($orchestrator->get_customizer());
		
		$tabletitle = new tabletitle($orchestrator->get_headline('Company'));
		$output->add($tabletitle);
		*/
		if($result){
		    return $result;
			//$output->add($report->get_table());
		} else {
		    echo "";
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