<?php
class dealprocessor_salesperson extends dealprocessor_selectfield{
	
	protected function get_javascript(){
		return 'add_salesperson();';
	}
	
	protected function get_default_id(){
		return '-1';
	}
	
	protected function get_default_val(){
		return 'Add Salesperson';
	}
	
	protected function get_option_list(){
		return sales::get_active_salespeople($this->deal->get_company_id());
	}
	
	protected function get_label(){
		return 'Add Salesperson';
	}
	
	public function validate(request &$request){
		return true;
	}
	
	public function set_deal_default(){
		
	}
}

