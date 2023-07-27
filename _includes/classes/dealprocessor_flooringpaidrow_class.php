<?php
class dealprocessor_flooringpaidrow extends dealprocessor_row{
	protected function get_child_list(){
		return array('flooring_paid', 'flooring_date');
	}
}