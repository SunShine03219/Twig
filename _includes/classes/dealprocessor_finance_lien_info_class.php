<?php
class dealprocessor_finance_lien_info extends dealprocessor_textareafield{
	
	protected function get_label(){
		return 'Lien Info';
	}
	
	protected function default_setting(){
		$setting = parent::default_setting();
		$setting->required = false;
		return $setting;
	}

}