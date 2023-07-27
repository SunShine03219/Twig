<?php
class dealprocessor_finance_gross extends dealprocessor_floatfield{
	protected function get_label(){
		return 'Misc Finance Gross';
	}
	
	public function calculate(){
		if($this->setting->setting == dealsetting::CALC){
			$finance_sale = json_decode($this->deal->get_finance_sale());
            $finance_cost = json_decode($this->deal->get_finance_cost());
			$gross = array_sum($finance_sale) - array_sum($finance_cost);
			$this->deal->set_finance_gross($gross);
		}
	}
	
	protected function default_setting(){
		$setting = parent::default_setting();
		$setting->setting = dealsetting::CALC;
		return $setting;
	}
}