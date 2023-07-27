<?php

class reportorderbuilder{
		
	private $id;
	private $dir;
	
	public function parse_get($defid='', $defdir='', $prefix = ''){
		//$this->set_dir(trim(urldecode($_GET[$prefix . 'sortdir'])));
		//$this->set_id(trim(urldecode($_GET[$prefix . 'sortid'])));
		
		if(empty($this->id) && empty($this->dir)){
			$this->dir = $defdir;
			$this->id = $defid;
		}
		
		return $this->get_active_sort();
	}
	
	private function validate_dir($dir){
		return (strtoupper($dir) == 'ASC' || strtoupper($dir) == 'DESC'); 
	}
	
	private function validate_id($id){
		//can handle this later, but for now any bad IDs will just fall-out
		return true;
	}
	
	private function sortid(){
		return strtolower($this->id);
	}
	
	private function sortdir(){
		return strtolower($this->dir);
	}
	
	private function sqlid(){
		return strtolower($this->id);
	}
	
	private function sqldir(){
		return strtoupper($this->dir);
	}
	
	public function build_orderclause(){
		if(!empty($this->id) && !empty($this->dir)){
			return ' ORDER BY ' . $this->sqlid() . ' ' . $this->sqldir();
		} else {
			return '';
		}
		
	}
	
	public function get_active_sort(){
		return array('id'=>$this->sortid(), 'dir'=>$this->sortdir());
	}
	
	public function set_id($id){
		$id = preg_replace('/\s+/', '', $id);
		if($this->validate_id($id)){
			$this->id = $id;
		}
	}
	
	public function set_dir($dir){
		if($this->validate_dir($dir)){
			$this->dir = $dir;
		}
	}
	
}