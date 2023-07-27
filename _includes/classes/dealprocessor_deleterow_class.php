<?php

class dealprocessor_deleterow extends dealprocessor_row{
	
	protected function get_child_list(){
		return array('deleted');
	}
	
}