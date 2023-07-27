<?php

abstract class dealprocessor_leaf extends dealprocessor_base{
	
	public function set_deal_default(){
		$functionname = 'set_'.$this->name;
		$this->deal->$functionname($this->setting->value);
	}
	
	public function build_form(formbuilder &$form, $mode = ''){
		if($this->setting->setting == dealsetting::DISPLAY){
			$this->display($form, $mode);
		}
	}

	abstract protected function get_label();
	
	abstract protected function display(formbuilder &$form, $mode = '');
	
	public function commit(){}
	
}