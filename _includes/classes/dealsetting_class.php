<?php

class dealsetting{
	
	public $name;
	public $setting;
	public $value;
	public $required;
	
	const DISPLAY = 0;
	const SKIP = 1;
	const HIDDEN = 2;
	const CALC = 3;
	
	public function __construct(){
		$this->name = '';
		$this->setting = self::DISPLAY;
		$this->value = 0;
		$this->set_required(false);
	}
	
	public function to_array(){
		return array(
				'name' => $this->name,
				'setting' => $this->setting,
				'value' => $this->value,
				'required' => $this->required
				);
	}
	
	public function set_required($v){
		$this->required = ($v?1:0);
	}
	
	public function from_array($array){
		$this->name = $array['name'];
		$this->setting = $array['setting'];
		$this->value = $array['value'];
		$this->set_required($array['required']);
	}
	
	public static function get_db_columnlist(){
		return array('name', 'setting', 'value', 'required');
	}
		
}