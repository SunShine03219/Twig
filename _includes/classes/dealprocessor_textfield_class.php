<?php
abstract class dealprocessor_textfield extends dealprocessor_leaf{
	
	protected function display(formbuilder &$form, $mode = ''){
		$functionname = 'get_'.$this->name;
		$form->add_text($this->name, $this->get_label(), $this->deal->$functionname(), $this->setting->required, $mode!='readonly');
	}
	
	public function validate(request &$request){
		$fieldname = $this->name;
		$field = $request->$fieldname;
		$cleanfield = htmlspecialchars(trim($field));
		
		if($this->setting->required && empty($cleanfield)){
			$request->add_error($this->name, $this->get_label() . ' is required');
			return false;
		} else {		
			$functionname = 'set_'.$this->name;
			$this->deal->$functionname($cleanfield);
			return true;
		}
	}
	
	public function calculate(){
		
	}
}