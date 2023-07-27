<?php

class reportcolumn {
	
	private $id;
	private $caption;
	private $width;
	private $sort;
	private $class;
	
	public function __get($name){
		switch($name){
			case 'id':
				return $this->id;
			case 'caption':
				return $this->caption;
			case 'width':
				return $this->width;
			case 'sort':
				return $this->sort;
			case 'class':
				return $this->class;
			default:
				throw new Exception('Invalid variable ' . $name);
		}
	}
	
	public function __construct($id=NULL, $caption=NULL, $width=NULL, $sort=NULL, $class=NULL){
		$this->id = $id;
		$this->caption = $caption;
		$this->width = $width;
		$this->sort = $sort;
		$this->class = $class;
	}
}