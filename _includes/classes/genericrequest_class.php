<?php
class genericrequest{
	private $vars;
	private $mode;
	
	public function __construct(){
		$this->mode = strtolower($_SERVER['REQUEST_METHOD']);
		$this->vars = array();
	}
	
	public function __get($name){
		return (isset($this->vars[$name]) ? $this->vars[$name] : '');
	}
	
	public function __set($name, $val){
		$this->vars[$name] = $val;
	}
	
	public function get_mode(){
		return $this->mode;
	}
	
	public function add_array($array){
		$this->vars = array_merge($this->vars, $array);
	}
	
	public function get_data(){
		return $this->vars;
	}
}