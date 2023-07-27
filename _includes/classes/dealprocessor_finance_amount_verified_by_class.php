<?php
class dealprocessor_finance_amount_verified_by extends dealprocessor_textfield{
	
	protected function get_label(){
		return 'Amount Verified By';
	}
	
	protected function default_setting(){
		$setting = parent::default_setting();
		$setting->required = false;
		return $setting;
	}

}