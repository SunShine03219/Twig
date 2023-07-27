<?php
class dealprocessor_date_sold extends dealprocessor_datefield{
	
	public function set_deal_default(){
		$this->deal->set_date_sold(date('Y-m-d', strtotime('today')));
	}
	
	protected function get_label(){
		return 'Date Sold';
	}
	
}