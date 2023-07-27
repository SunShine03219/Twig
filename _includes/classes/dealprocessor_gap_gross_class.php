<?php
class dealprocessor_gap_gross extends dealprocessor_floatfield{
	protected function get_label(){
		return 'Gap Gross';
	}
	
	public function calculate(){
		if($this->setting->setting == dealsetting::CALC){
			$gross = $this->deal->get_gap_sale() - $this->deal->get_gap_cost();
			$this->deal->set_gap_gross($gross);
		}
	}
	
	protected function default_setting(){
		$setting = parent::default_setting();
		$setting->setting = dealsetting::CALC;
		return $setting;
	}
}