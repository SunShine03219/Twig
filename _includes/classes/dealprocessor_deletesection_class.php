<?php
class dealprocessor_deletesection extends dealprocessor_section{
	
	protected function get_caption(){
		return 'Delete Deal';
	}
	
	protected function get_child_list(){
		return array('deleterow');
	}
	
}