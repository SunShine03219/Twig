<?php
abstract class dealprocessor_selectfield extends dealprocessor_leaf{
	
	protected function display(formbuilder &$form, $mode = ''){
		//yeah I know, but this is how it's giong to roll
		$javascript = $this->get_javascript();
		if(empty($javascript)){
			$form->add_select($this->name, $this->get_option_list(), $this->get_label(), $this->get_default_id(), $this->get_default_val(), $this->setting->required, $mode=='readonly');
		} else {
			$form->add_select_with_javascript($this->name, $this->get_option_list(), $this->get_javascript(), $this->get_label(), $this->get_default_id(), $this->get_default_val(), $this->setting->required, $mode=='readonly');
		}
		
	}
	
	protected function get_javascript(){
		return '';
	}

	abstract protected function get_option_list();
	
	protected function get_default_id(){
		$functionname = 'get_'.$this->name;
		return $this->deal->$functionname();
	}
	
	abstract protected function get_default_val();

	public function validate(request &$request){
		$fieldname = $this->name;
		$field = $request->$fieldname;
		$cleanfield = intval($field);
		
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