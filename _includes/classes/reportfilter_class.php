<?php

abstract class reportfilter implements icustomizer{
	
	protected $hidden;
	protected $invisible;
	
	public function __construct($hidden = false, $invisible = false){
		$this->set_hidden($hidden);
		$this->set_invisible($invisible);
	}
	
	public function handle(&$array){}
	
	public function build(formbuilder &$form){}
	
	public function wheres(reportwherebuilder &$wherebuilder){}
	
	public function set_hidden($v){
		$this->hidden = ($v?true:false);
	}
	
	public function hide(){
		$this->set_hidden(true);
	}
	
	public function show(){
		$this->set_hidden(false);
	}
	
	public function set_invisible($v){
		$this->invisible = ($v?true:false);
	}
	
	public function headline($type){
		return false;
	}
	
}