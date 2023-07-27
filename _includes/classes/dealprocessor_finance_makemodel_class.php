<?php
class dealprocessor_finance_makemodel extends dealprocessor_textfield{
	
	protected function get_label(){
		return 'Make model';
	}
	
	protected function default_setting(){
		$setting = parent::default_setting();
		$setting->required = false;
		return $setting;
	}

}