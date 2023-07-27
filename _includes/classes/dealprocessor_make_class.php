<?php
class dealprocessor_make extends dealprocessor_textfield{
	
	protected function get_label(){
		return 'Make';
	}
	
	protected function default_setting(){
		$setting = parent::default_setting();
		$setting->required = true;
		return $setting;
	}
	
}