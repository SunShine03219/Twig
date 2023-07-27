<?php

class dealprocessor_dealer_check extends dealprocessor_floatfield{
	
	protected function get_label(){
		return 'Dealer Check';
	}
	
	public function calculate(){
		if($this->setting->setting == dealsetting::CALC){
			$check = $this->deal->get_amount_financed() - $this->deal->get_discount();
			$this->deal->set_dealer_check($check);
		}
	}
	
	protected function default_setting(){
		$setting = parent::default_setting();
		$setting->setting = dealsetting::CALC;
		return $setting;
	}
	
}