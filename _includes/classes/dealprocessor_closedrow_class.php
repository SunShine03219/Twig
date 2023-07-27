<?php
class dealprocessor_closedrow extends dealprocessor_row{
	protected function get_child_list(){
		return array('closed_dms');
	}
}