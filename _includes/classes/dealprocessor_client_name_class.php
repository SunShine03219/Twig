<?php
class dealprocessor_client_name extends dealprocessor_textfield{
	
	protected function get_label(){
		return 'Client Name';
	}
	
	protected function default_setting(){
		$setting = parent::default_setting();
		$setting->required = true;
		return $setting;
	}

}