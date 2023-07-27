<?php
class dealprocessor_sale_price_funding extends dealprocessor_textfield{
	
	protected function get_label(){
		return 'Sale Price Funding';
	}
	
	protected function default_setting(){
		$setting = parent::default_setting();
		$setting->required = false;
		return $setting;
	}

}