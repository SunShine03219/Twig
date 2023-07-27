<?php
class dealprocessor_finance_person extends dealprocessor_selectwithnothingfield{
	protected function get_options(){
                
		return finance::get_active_financepeople($this->deal->get_company_id());
	}
	
	protected function get_label(){
		return 'Finance Person';
	}
	
	protected function get_default_val(){
		return finance::get_financeperson_name($this->deal->get_finance_person());
	}
	
}