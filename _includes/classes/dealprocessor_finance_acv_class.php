<?php
class dealprocessor_finance_acv extends dealprocessor_textfield{
	
	protected function get_label(){
		return 'ACV';
	}
	
	protected function default_setting(){
		$setting = parent::default_setting();
		$setting->required = false;
		return $setting;
	}

}