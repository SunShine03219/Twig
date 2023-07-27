<?php
class dealprocessor_frontgrossrow extends dealprocessor_row{
	
	protected function get_child_list(){
		return array('initial_gross', 'payable_gross','newcar', 'sale_price_funding');
	}
}