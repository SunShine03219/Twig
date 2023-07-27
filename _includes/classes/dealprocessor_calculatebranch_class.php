<?php
// as of yet this is not used because these fields are always computed at the report level.

class dealprocessor_calculatebranch extends dealprocessor_branch{
	
	protected function get_label(){
		return 'Automatic Calculations';
	}
	
	protected function get_child_list(){
		return array('frontgross', 'backgross', 'total_gross');
	}
}