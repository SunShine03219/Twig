<?php
abstract class dealprocessor_hiddenfield extends dealprocessor_leaf{
	
	protected function display(formbuilder &$form, $mode = ''){
		$functionname = 'get_'.$this->name;
		$form->add_hidden($this->name, $this->deal->$functionname());
	}
	
	public function calculate(){
		
	}
	
	public function get_label(){
		
	}
	
}