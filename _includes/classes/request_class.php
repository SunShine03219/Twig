<?php
class request{
	
	private $data;
	private $error_msg;
	private $errors;
	
	public function __construct(&$data = null){
		if(is_null($data)){
			$this->data = array();
		} else {
			$this->data = $data;
		}
		
		$this->error_msg = '';
		$this->errors = array();
	}
	
	public function __get($name){
		return (isset($this->data[$name])?$this->data[$name]:null);
	}
	
	public function has_errors(){
		return (count($this->errors)>0);
	}
	
	public function set_error_msg($msg){
		$this->error_msg = $msg;
	}
	
	public function add_error($field, $message){
		$this->errors[$field] = $message;
	}
	
	public function add_array($array){
		foreach($array as $key=>$value){
			$this->add_field($key, $value);
		}
	}
	
	public function add_field($key, $value){
		$this->data[$key] = $value;
	}
	
	public function get_data(){
		return $this->data;
	}
	
	public function get_errors(){
		return $this->errors;
	}
	
	public function get_error_msg(){
		return $this->error_msg;
	}
	
	public function has_error($field){
		if(isset($this->data[$field])){
			return $this->data[$field];
		} else {
			return false;
		}
	}
	
	public function get_fields($fields){
		if(is_array($fields)){
			$array = array();
			foreach($fields as $field){
				$array[$field] = $this->$field;
			}
			return $array;
		}
		return array();
	}
}