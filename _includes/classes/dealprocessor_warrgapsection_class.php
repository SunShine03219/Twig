<?php
class dealprocessor_warrgapsection extends dealprocessor_section{
	protected function get_caption(){
		return 'Warranty and Gap Insurance Information';
	}
	
	protected function get_child_list(){
		return array('warrantyrow', 'gaprow', 'miscfinrow', 'reserverow', 'warrgapquickaddrow');
	}
}