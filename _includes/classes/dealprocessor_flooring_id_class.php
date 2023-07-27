<?php
class dealprocessor_flooring_id extends dealprocessor_selectwithnothingfield{
	
	protected function get_options(){
		return flooring::get_active_flooring_companies($this->deal->get_company_id());
	}
	
	protected function get_label(){
		return 'Floored By';
	}
	
	protected function get_default_val(){
		return flooring::get_flooring_name($this->deal->get_flooring_id());
	}
	
}