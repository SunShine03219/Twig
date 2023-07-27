<?php
class dealprocessor_lender_id extends dealprocessor_selectwithnothingfield{
	
	protected function get_options(){
		return lender::get_active_lenders($this->deal->get_company_id());
	}
	
	protected function get_label(){
		return 'Lending Source';
	}
	
	protected function get_default_val(){
		return lender::get_lender_name($this->deal->get_lender_id());
	}
	
}