<?php
class dealprocessor_financedatesrow extends dealprocessor_row{
	protected function get_child_list(){
		return array('date_sent', 'funded_date');
	}
}