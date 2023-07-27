<?php
class buildablecollection implements ibuildable{
	
	private $objects;
	
	public function __construct(){
		$this->objects = array();
	}
	
	public function add(ibuildable &$object){
		$this->objects[] = $object;
	}
	
	public function add_before(ibuildable &$object){
		array_unshift($this->objects, $object);
	}
	
	public function build(page &$page){
		foreach($this->objects as $object){
			$object->build($page);
		}
	}
} 