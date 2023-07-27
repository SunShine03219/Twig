<?php
class dealprocessor_warranty_id extends dealprocessor_selectwithnothingfield{
	protected function get_options(){
		return warranty::get_active_warranty($this->deal->get_company_id());
	}
	
	protected function get_label(){
		return 'Warranty';
	}
	
	protected function get_default_val(){
		return warranty::get_warranty_name($this->deal->get_warranty_id());
	}
}