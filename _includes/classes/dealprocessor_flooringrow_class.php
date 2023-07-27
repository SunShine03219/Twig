<?php
class dealprocessor_flooringrow extends dealprocessor_row{
	protected function get_child_list(){
		return array('flooring_id', 'flooringquickadd');
		//'flooring_paid', 'flooring_date'
	}
}