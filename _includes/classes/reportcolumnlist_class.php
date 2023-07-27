<?php

class reportcolumnlist{
	private $columns;
		
	const PATTERN_MATCH = '/\{([\w_]*)\}/'; //basicaly match brace enclosed as $m[1]
	const MATCH_FORMAT = '{%s}'; //sprintf format string, brace enclose
	
	public function __construct(){
		$this->columns = array();
		$this->reset_cache();
	}
	
	public function add($id=NULL, $caption=NULL, $width=NULL, $sort=NULL, $class=NULL){
		array_push($this->columns, new reportcolumn($id, $caption, $width, $sort, $class));
		$this->reset_cache();
	}
	
	private function reset_cache(){
		$this->builtcaptions = '';
		$this->builtdata = '';
		$this->builtrow = '';
		$this->builtwidths = '';
	}
	
	public function get_order(){
		$order = array();
		foreach($this->columns as $col){
			$order[] = $col->id;
		}
		return $order;
	}
	
	public function get_widths(){
		$has_widths = false;
		$widths = array();
		foreach($this->columns as $col){
			$width = $col->width;
			if(!empty($width)){
				$widths[] = $width;
				$has_widths = true;
			}
		}
		if(!$has_widths){
			return false;
		}
		return $widths;
	}
	
	public function get_captions(){
		$captions = array();
		foreach($this->columns as $col){
			$captions[] = $col->caption;
		}
		return $captions;
	}
	
	public function build_captions(){
		if(empty($this->builtcaptions)){
			$this->builtcaptions = $this->rebuild_captions();
		}
		return $this->builtcaptions;
	}
	
	public function map_id_caption(){
		foreach($this->columns as $col){
			$map[$col->id] = $col->caption;
		}
		return $map;
	}
	
	public function get_enabled_sorts(){
		$sorts = array();
		foreach($this->columns as $col){
			$sort = $col->sort;
			if(!empty($sort)){
				$sorts[] = $col->sort;
			}
		}
		return $sorts;
	}
	
	public function map_id_sort(){
		foreach($this->columns as $col){
			$map[$col->id] = $col->sort;
		}
		return $map;
	}
	
	public function map_id_class(){
		foreach($this->columns as $col){
			$map[$col->id] = $col->class;
		}
		return $map;
	}
	
	private function rebuild_captions(){
		$subject = $this->build_row();
		$data = $this->build_map_id_caption();
		$callback = $this->retrieve_callback($data);
		$output = preg_replace_callback($this->retrieve_pattern(),
				$callback, $subject);
		return $output;
	}

	public function build_data(){
		if(empty($this->builtdata)){
			$this->builtdata = $this->rebuild_data();
		}
		return $this->builtdata;
	}
	
	private function get_format_id_class(){
		$a = array();
		foreach($this->columns as $col){
			$a[] = array('id'=>$this->format_id($col->id),
					'class'=>$this->format_class($col->class));
		}
		return $a;
	}
}