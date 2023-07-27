<?php

class dealprocessor_datesoldrow extends dealprocessor_row{
	protected function get_child_list(){
		return array('date_sold', 'miles');
	}
}