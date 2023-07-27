<?php
class dealprocessor_miscfinrow extends dealprocessor_row{
	protected function get_child_list(){
		return array('finance_placeholder', 'finance_sale', 'finance_cost', 'finance_gross');
	}
}