<?php
class togglefrombuilder extends reportfrombuilder{
	
	private $table;
	private $short;
	
	public function __construct($table, $short){
		$this->table = $table;
		$this->short = $short;
	}
	
	public function build_fromclause(){
		return ' FROM `'.$this->table.'` '.$this->short.' ';
	}
	
}