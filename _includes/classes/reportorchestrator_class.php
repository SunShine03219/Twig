<?php

class reportorchestrator{
	
	private $customizers;
	private $filters;
	private $default;
	private $mode;
	
	public function __construct(){
		$this->customizers = array();
		$this->filters = array();
		$this->default = array();
		$this->mode = '';
	}
	
	public function get_mode(){
		return $this->mode;
	}
	
	public function add_customizer(icustomizer &$customizer){
		$this->customizers[] = $customizer;
	}
	
	public function set_defaults($defaults){
		$this->default = $defaults;
	}
	
	public function build(formbuilder &$form){
		$this->build_customizers($form);
	}
	
	private function build_customizers(formbuilder &$form){
		foreach($this->customizers as $customizer){
			$output[] = $customizer->build($form);
		}
		return $output;
	}
	
	public function combine_wheres(reportwherebuilder $wherebuilder){
		foreach($this->customizers as $customizer){
			$customizer->wheres($wherebuilder);
		}
	}
	
	public function process_array($array = null){
		if(is_null($array)){$array = array();}
		$active = array_merge($this->default, $array);
		$this->mode = (isset($active['mode']) ? $active['mode'] : '');
		foreach($this->customizers as $customizer){
			$customizer->handle($active);
		}
	}
	
	public function get_headline($type){
		$headline = $type . ' Report';
		foreach($this->customizers as $customizer){
			$result = $customizer->headline($type);
			if($result){
				$headline = $result;
			}
		}
		return $headline;
	}
	
	public function get_active_vars(){
		$vars = array();
		foreach($this->customizers as $customizer){
			$result = $customizer->get_active_vars();
			if(is_array($result)){
				$vars = array_merge($vars, $result);
			}
		}
		return $vars;
	}
	
	public function get_customizer($action = ''){
		$form = new formbuilder();
		$form->setup('customizer', $_SERVER['SCRIPT_NAME'], 'GET');
		$form->add_subtitle('Customize Report');
		$this->build($form);
		$form->add_submit();
		if(!empty($action)){
			$form->add_hidden('action', $action);
		}
		$customizerform = new customizerwrapper($form);
		return $customizerform;
	}
	
}