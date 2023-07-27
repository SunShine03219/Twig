<?php
class pickuppaymentview{
	
	public function invoke(){
		
		$output = new buildablecollection();

		//print("Before Here 2");

		$defaults = array(
			filterpaid::PAID=>filterpaid::ONLY_UNPAID,
			filterdeletedpayments::ONLY_NOT_DELETED,
			filtercompanyid::COMPANY_ID => $_SESSION['FT']['company_id']
			);		
			
		$orchestrator = new reportorchestrator();
		$orchestrator->add_customizer(new filterpaid());
		$orchestrator->add_customizer(new filterdeletedpayments());
		$orchestrator->add_customizer(new filtercompanyid());
		$orchestrator->set_defaults($defaults);
		$orchestrator->process_array($_GET);
		
		$wherebuilder = new reportwherebuilder();
		$frombuilder = new togglefrombuilder(pickuppayment::TABLE, 'pp');
		$orderbuilder = new reportorderbuilder();
		$orderbuilder->parse_get('date_due', 'asc');
		
		$orchestrator->combine_wheres($wherebuilder);
		
		$report = new pickuppaymentreport();
		$report->add_other_vars($orchestrator->get_active_vars());
		$result = $report->execute($wherebuilder, $frombuilder, $orderbuilder);
		
		return $result;
		
		$output->add($orchestrator->get_customizer());
		
		$tabletitle = new tabletitle($orchestrator->get_headline('Deferred Down Payments'));
		$tabletitle->add_ext_js('scripts/popnotes.js');
		$output->add($tabletitle);
		
		if($result){
			$output->add($report->get_table());
		} else {
			$output->add($this->build_error($report->build_error_no_result()));
		}
		
		return $output;
		
	}
	
	private function build_error($msg){
		$buildable = new basicbuildable();
		$buildable->add_content($msg, 'error');
		return $buildable;
	}
	
}