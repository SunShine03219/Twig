<?php
abstract class dealprocessor_placeholderfield extends dealprocessor_leaf{
	
	protected function display(formbuilder &$form, $mode=''){
		$form->add_custom('', $this->get_label());
	}
	
	public function validate(request &$request){
		return true;
	}
	
	public function calculate(){
		
	}
	
	public function set_deal_default(){
		
	}
	
}