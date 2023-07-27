<?php

class dealprocessor_valuenew extends dealprocessor_row{
	protected function get_child_list(){
		return array('holdback');
	}
}