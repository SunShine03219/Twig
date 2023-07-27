<?php
class dealprocessor_deskmanagerrow extends dealprocessor_row{
	
	protected function get_child_list(){
		return array('deskmanager', 'deskmanagerquickadd');
	}
	
}