<?php
class dealprocessor_client_phone extends dealprocessor_textfield{
	
	protected function get_label(){
		return 'Client Phone';
	}
	
	protected function default_setting(){
		$setting = parent::default_setting();
		$setting->required = false;
		return $setting;
	}

}