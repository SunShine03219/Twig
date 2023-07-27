<?php

abstract class dealprocessor_selectwithnothingfield extends dealprocessor_selectfield{
	
	protected function get_option_list(){
		$values = $this->get_options();
		array_unshift($values, array('id' => '', 'val' => 'None'));
		return $values;
	}
	
	abstract protected function get_options();
	

}