<?php
class dealprocessor_leadsection extends dealprocessor_section{
	
	protected function get_caption(){
		return 'Lead Source';
	}
	
	protected function get_child_list(){
		return array('leadrow');
	}
	
}