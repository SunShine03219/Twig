<?php
class dealprocessor_model extends dealprocessor_textfield{
	
	protected function get_label(){
		return 'Model';
	}
	
	protected function default_setting(){
		$setting = parent::default_setting();
		$setting->required = true;
		return $setting;
	}
	
}