<?php
class dealviewcontroller{
	
	public function invoke($type, $caption){
		//$output = new buildablecollection();
		$orchestrator = $this->retrieve_orchestrator($type);
		
		$orchestrator->process_array($_GET);
		// print_r($_GET);die();
		
		$wherebuilder = new reportwherebuilder();
		$frombuilder = new reportfrombuilder();
		if($type == 'search'){
			//$frombuilder = new togglefrombuilder('searchview', 'sv');
		}
		$orderbuilder = new reportorderbuilder();
		$orderbuilder->parse_get('date_sold', 'desc');
		
		$orchestrator->combine_wheres($wherebuilder);
		
		$reportname = $type . 'dealreport';
		$report = new $reportname();
		$report->add_other_vars($orchestrator->get_active_vars());

		$result = $report->execute($wherebuilder, $frombuilder, $orderbuilder);
		
		//$output->add($orchestrator->get_customizer($type));
		
		//$headline = $orchestrator->get_headline($caption);
		//$tabletitle = new tabletitle($headline);
		//$tabletitle->add_ext_js('scripts/popnotes.js');
		//$output->add($tabletitle);
		
		//$pagetitle = new buildablepagetitle($headline);
		//$output->add($pagetitle);
		
		if($result){
			//$output->add($report->get_table());
			return $result;
		} else {
			//$output->add($this->build_error('No Deals Found'));
			//echo 'No Deals Found';
		}
		
		//return $output;
	}
	
	private function retrieve_defaults($type){
		$defaults = array(
				filtercompanyid::COMPANY_ID => $_SESSION['FT']['company_id'],
				filterdeleteddeals::DELETED_DEALS => filterdeleteddeals::ONLY_NOT_DELETED,
				);
		switch($type){
			case 'unfunded':
				$defaults[filterfunded::FUNDED] = filterfunded::ONLY_UNFUNDED;
				break;
			case 'funded':
				$defaults[filterfunded::FUNDED] = filterfunded::ONLY_FUNDED;
				$defaults[filterdmsstatus::DMS_STATUS] = filterdmsstatus::ONLY_OPEN;
				break;
			case 'approved':
				$defaults['approve'] = 1;
				break;
			case 'pending':
				$defaults['pending'] = 1;
				break;
			case 'close':
				$defaults[filterfunded::FUNDED] = filterfunded::ONLY_FUNDED;
				$defaults[filterdmsstatus::DMS_STATUS_COLUMN] = filterdmsstatus::ONLY_CLOSED;
				break;
			case 'delete':
				$defaults[filterdeleteddeals::DELETED_DEALS] = filterdeleteddeals::ONLY_DELETED;
				break;
			case 'floored':
				$defaults[filterfloored::FLOORED] = filterfloored::ONLY_FLOORED;				
				$defaults[filterdmsstatus::DMS_STATUS] = filterdmsstatus::ONLY_OPEN;
				break;
			case 'search':
				break;
			case 'view':
				$defaults['mode'] = customizermonthly::MODE_MTD;
				break;
		}
		return $defaults;
	}
	
	private function retrieve_orchestrator($type){
		$orchestrator = new reportorchestrator();
		$orchestrator->add_customizer(new filtercompanyid());
		
		switch($type){
			case 'unfunded':
				$orchestrator->add_customizer(new filterfunded(true));
				$orchestrator->add_customizer(new filterdeleteddeals(FALSE));
				break;
			case 'funded':
				$orchestrator->add_customizer(new filterfunded(true));
				$orchestrator->add_customizer(new filterdmsstatus(true));
				$orchestrator->add_customizer(new filterdeleteddeals(FALSE));
				break;
			case 'approved':
				$orchestrator->add_customizer(new filterapproved(true));
				break;
			case 'pending':
				$orchestrator->add_customizer(new filterpending(true));
				break;
			case 'close':
				$orchestrator->add_customizer(new filterfunded(true));
				$orchestrator->add_customizer(new filterdmsstatus(true));
				$orchestrator->add_customizer(new filterdeleteddeals(FALSE));
				break;
			case 'delete':
				$orchestrator->add_customizer(new filterdmsstatus(true));
				$orchestrator->add_customizer(new filterdeleteddeals(TRUE));
				break;
			case 'floored':
				$orchestrator->add_customizer(new filterfloored(true));
				$orchestrator->add_customizer(new filterdmsstatus(false));
				$orchestrator->add_customizer(new filterdeleteddeals(FALSE));
				break;
			case 'search':
				$orchestrator->add_customizer(new filterdeleteddeals(false));
				$orchestrator->add_customizer(new filtersearch());
				break;
			case 'view':
			    /*
				$orchestrator->add_customizer(new customizermonthly());
				$orchestrator->add_customizer(new customizerdaily());
				$orchestrator->add_customizer(new customizerdaterange());
				$orchestrator->add_customizer(new customizernodate());
				*/
				$orchestrator->add_customizer(new filterdeleteddeals(FALSE));
				break;
		}
		
		
		$orchestrator->set_defaults($this->retrieve_defaults($type));
		return $orchestrator;
	}
	
	private function build_error($msg){
		$buildable = new basicbuildable();
		$buildable->add_content($msg, 'error');
		return $buildable;
	}
	
}