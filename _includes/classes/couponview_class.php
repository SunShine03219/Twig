<?php

class couponview{
	
	public function invoke(){
		$output = new buildablecollection();
		
		$defaults = array(filteractive::ACTIVE_ITEMS=>filteractive::ONLY_ACTIVE,
				filtercompanyidtoggle::COMPANY_ID=>$_SESSION['FT']['company_id']);

		$orchestrator = new reportorchestrator();
		$orchestrator->add_customizer(new filtercompanyidtoggle(false, true));
		$orchestrator->set_defaults($defaults);
		$orchestrator->process_array($_GET);
		
		$wherebuilder = new reportwherebuilder();
		$frombuilder = new togglefrombuilder(coupon::TABLE, 'c');
		$orderbuilder = new reportorderbuilder();
		$orderbuilder->parse_get('title', 'asc');
		
		$orchestrator->combine_wheres($wherebuilder);

		$report = new couponreport();
		$report->add_other_vars($orchestrator->get_active_vars());
		$result = $report->execute($wherebuilder, $frombuilder, $orderbuilder);
		

		if($result){
		    return $result;
		} else {
		    echo "No Coupons Found";
		}
		
		return $output;
	}
	
	private function build_error($msg){
		$buildable = new basicbuildable();
		$buildable->add_content($msg, 'error');
		return $buildable;
	}
	
}