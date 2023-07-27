<?php
abstract class dealprocessor_datefield extends dealprocessor_leaf{
	
	protected function display(formbuilder &$form, $mode=''){
		$functionname = 'get_'.$this->name;
		$default = $this->deal->$functionname();
		$form->add_datepicker($this->name, $default, $this->get_label(), $this->setting->required);
	}
	
	public function validate(request &$request){
		$fieldname = $this->name;
		$field = $request->$fieldname;
		
		if(!empty($field)){
			if(!$timestamp = strtotime($field)){
				$request->add_error($this->name, $this->get_label() . ' invalid date');
				return false;
			} else {
				$cleanfield = date('Y-m-d', $timestamp);
				$functionname = 'set_'.$this->name;
				$this->deal->$functionname($cleanfield);
				return true;
			}
		}
		return true; //empty is ok, no required dates
	}
	
	public function calculate(){
	}
	
}