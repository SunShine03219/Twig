<?php
class dealprocessor_closedealsection extends dealprocessor_section{
	protected function get_caption(){
		return 'DMS Status';
	}
	
	protected function get_child_list(){
		return array('closedrow');
	}
}