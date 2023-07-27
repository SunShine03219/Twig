<?php
class dealprocessor_holdback extends dealprocessor_textfield{
	
	protected function get_label(){
		return 'Holdback';
	}
	
	protected function default_setting(){
		$setting = parent::default_setting();
		return $setting;
	}

}