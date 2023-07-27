<?php
class dealprocessor_custvehsection extends dealprocessor_section{
	
	protected function get_caption(){
		return 'Customer and Vehicle Information';
	}
	
	protected function get_child_list(){
		return array('valuenew', 'clientrow', 'vehiclerow', 'datesoldrow', 'frontgrossrow', 'frontextrarow', 'deskmanagerrow', 'salespersonrow', 'saleslistrow');
	}
	
}