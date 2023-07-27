<?php
class dealprocessor_reserverow extends dealprocessor_row{
	protected function get_child_list(){
		return array('reserve');
	}
}