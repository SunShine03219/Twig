<?php
class dealprocessor_finance_vehicle_notes extends dealprocessor_textareafield{
	
	protected function get_label(){
		return 'Vehicle Notes';
	}
	
	protected function default_setting(){
		$setting = parent::default_setting();
		$setting->required = false;
		return $setting;
	}

}