<?php
abstract class dealprocessor_floatfield extends dealprocessor_leaf{
	
	protected function display(formbuilder &$form, $mode = ''){
		$functionname = 'get_'.$this->name;
		$form->add_text($this->name, $this->get_label(), $this->deal->$functionname(), $this->setting->required, $mode!='readonly');
	}
	
	public function validate(request &$request){
		$fieldname = $this->name;
		$field = $request->$fieldname;
		// echo "fieldname : ".$fieldname;
		// echo "<br>";
		// echo "field : ".$field;
		// echo "<br>";
		// die;
		if(!empty($field)){
			if(is_array($field))
			{  
				// $cleanfield = floatval($field);
				// $functionname = 'set_'.$this->name;
				// $testts = $this->deal->$functionname($cleanfield);
				
				return true;

			 
			  }
							elseif(!is_numeric($field)){
				$request->add_error($this->name, $this->get_label() . ' must be a  number');
				return false;
			} else {
				$cleanfield = floatval($field);
			}
		} else {
			$cleanfield = 0;
		}
		
		if($this->setting->required && empty($cleanfield) && $cleanfield !== 0.0){
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