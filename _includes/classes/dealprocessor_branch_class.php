<?php

abstract class dealprocessor_branch extends dealprocessor_base{
	
	private $children;
	
	public function __construct(dealprocessor_factory &$factory, deal &$deal, dealsetting &$setting = null){
		parent::__construct($factory, $deal, $setting);
		$this->children = array();
		$this->make_children($this->get_child_list());
	}
	
	abstract protected function get_child_list();
	
	public function set_deal_default(){
		if($this->setting->setting != dealsetting::SKIP){
			$this->child_deal_defaults();
		}
	}
	
	public function build_form(formbuilder &$form, $mode = ''){
		if($this->setting->setting != dealsetting::SKIP){
			$this->build_children($form, $mode);
		}
	}
	
	protected function build_form_special_modes(formbuilder &$form, $modearray){
		foreach($this->children as $child){
			$child->build_form($form, $modearray[$child->name]);
		}
	}
	
	public function calculate(){
		if($this->setting->setting != dealsetting::SKIP){
			$this->calculate_children();
		}
	}
	
	public function validate(request &$request){
		if($this->setting->setting != dealsetting::SKIP){
			return $this->validate_children($request);
		} else {
			return true;
		}
	}
	
	protected function make_children(array $children){
		
		if(!empty($children) && is_array($children)){
			foreach($children as $child){
				$this->children[] = $this->factory->make($child, $this->deal);
			}
		}
	}
	
	protected function build_children(formbuilder &$form, $mode){
		foreach($this->children as $child){
			$child->build_form($form, $mode);
		}
	}
	
	protected function calculate_children(){
		foreach($this->children as $child){
			$child->calculate();
		}
	}
	
	protected function validate_children(request &$request){
		$valid = true;
		foreach($this->children as $child){
			$result = $child->validate($request);
			$valid = $valid && $result;
		}
		return $valid;
	}
	
	protected function validate_children_array(request &$request, $list = null){
		//mainly used as a hack for working with closed deals
		$valid = true;
		foreach($this->children as $child){
			$name = $child->get_name();
			if(is_array($list) && in_array($name, $list)){
				$result = $child->validate($request);
				$valid = $valid && $result;
			}
		}
		return $valid;
	}
	
	protected function child_deal_defaults(){
		foreach($this->children as $child){
			$child->set_deal_default();
		}
	}
	
	public function populate_settinglist(dealsettinglist &$settinglist){
		parent::populate_settinglist($settinglist);
		$this->child_populate_settinglist($settinglist);
	}
	
	protected function child_populate_settinglist(dealsettinglist &$settinglist){
		foreach($this->children as $child){
			$child->populate_settinglist(dealsettinglist &$settinglist);
		}
	}
	
	public function commit(){
		$this->commit_children();
	}
	
	protected function commit_children(){
		foreach($this->children as $child){
			$child->commit();
		}
	}
	
	
}