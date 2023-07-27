<?php
class dealprocessor_lead_id extends dealprocessor_selectwithnothingfield{
	
	protected function get_options(){
		return lead::get_active_lead_companies($this->deal->get_company_id());
	}
	
	protected function get_label(){
		return 'Lead Source';
	}
	
	protected function get_default_val(){
		return lead::get_lead_name($this->deal->get_lead_id());
	}
	
}