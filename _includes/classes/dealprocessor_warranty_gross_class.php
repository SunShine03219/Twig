<?php
class dealprocessor_warranty_gross extends dealprocessor_floatfield{
	protected function get_label(){
		return 'Warranty Gross';
	}
	
	public function calculate(){
		if($this->setting->setting == dealsetting::CALC){
			$gross = $this->deal->get_warranty_sale() - $this->deal->get_warranty_cost();
			$this->deal->set_warranty_gross($gross);
		}
	}
	
	protected function default_setting(){
		$setting = parent::default_setting();
		$setting->setting = dealsetting::CALC;
		return $setting;
	}
}