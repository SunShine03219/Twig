<?php
class dealprocessor_flooringsection extends dealprocessor_section{
	
	protected function get_caption(){
		return 'Flooring';
	}
	
	protected function get_child_list(){
		return array('flooringrow', 'flooringpaidrow');
	}
	
}