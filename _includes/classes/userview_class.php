<?php

class userview{
	
	public function invoke(){
		$output = new buildablecollection();
		
		$defaults = array(filteractive::ACTIVE_ITEMS=>filteractive::ONLY_ACTIVE,
				filtercompanyidtoggle::COMPANY_ID=>$_SESSION['FT']['company_id']);
		
		$orchestrator = new reportorchestrator();
		// $orchestrator->add_customizer(new filteractive());
		$orchestrator->add_customizer(new filtercompanyidtoggle(false, true));
		$orchestrator->set_defaults($defaults);
		$orchestrator->process_array($_GET);
		
		$wherebuilder = new reportwherebuilder();
		$frombuilder = new togglefrombuilder(user::TABLE, 't');
		$orderbuilder = new reportorderbuilder();
		$orderbuilder->parse_get('name', 'asc');
		
		$orchestrator->combine_wheres($wherebuilder);
		
		$report = new userreport();
		$report->add_other_vars($orchestrator->get_active_vars());
		$result = $report->execute($wherebuilder, $frombuilder, $orderbuilder);
		
		/*
		$output->add($orchestrator->get_customizer());
		
		$tabletitle = new tabletitle($orchestrator->get_headline('User'));
		$output->add($tabletitle);
		
		*/
		if($result){
		    return $result;
			//$output->add($report->get_table());
		} else {
		    echo "No Users Found";
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