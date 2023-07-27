<?php

class dealprocessor_initial_gross extends dealprocessor_floatfield{
	
	protected function get_label(){
		return 'Initial Gross';
	}
	
	protected function default_setting(){
		$setting = parent::default_setting();
		$setting->setting = dealsetting::SKIP;
		return $setting;
	}
	
	public function calculate(){
		
	}
	
}