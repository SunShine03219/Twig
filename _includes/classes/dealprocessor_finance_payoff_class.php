<?php
class dealprocessor_finance_payoff extends dealprocessor_selectwithnothingfield{

	protected function get_options(){
            return array(
				array('id'=>1,'val'=>'Yes'),
				array('id'=>0,'val'=>'No')
			); finance::get_active_financepayment($this->deal->get_company_id());
	}
	
	protected function get_label(){
		return 'Payoff';
	}
	
	protected function get_default_val(){
		return 1; //finance::get_financeperson_name($this->deal->get_finance_person());
	}
	
}