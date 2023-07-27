<?php
class dealprocessor_gap_id extends dealprocessor_selectwithnothingfield{	
	protected function get_options(){
		return gap::get_active_gap($this->deal->get_company_id());
	}
	
	protected function get_label(){
		return 'Gap Insurance';
	}
	
	protected function get_default_val(){
		return gap::get_gap_name($this->deal->get_finance_person());
	}
	
}