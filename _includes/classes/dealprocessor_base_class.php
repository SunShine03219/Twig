<?php

abstract class dealprocessor_base{
		
	protected $factory;
	protected $setting;
	protected $deal;
	protected $name;
	
	public function __construct(dealprocessor_factory &$factory, deal &$deal, dealsetting &$setting = null){
		$this->factory = $factory;
		
		$this->deal = $deal;
		
		$this->name = str_replace('dealprocessor_', '', get_class($this));
		
		if(is_null($setting)){
			$this->setting = $this->default_setting();
		} else {
			$this->setting = $setting;
		}
	}
	
	protected function default_setting(){
		$setting = new dealsetting();
		$setting->from_array(array('name'=>$this->name, 'setting'=>dealsetting::DISPLAY, 'value'=>''));
		return $setting;
	}
	
	abstract public function set_deal_default();
	
	abstract public function build_form(formbuilder &$form, $mode = '');
	
	abstract public function calculate();
	
	abstract public function validate(request &$request);
	
	abstract public function commit();
	
	public function populate_settinglist(dealsettinglist &$settinglist){
		$settinglist->add_setting($this->get_setting());
	}
	
	public function get_setting(){
		return $this->setting;
	}
	
	public function get_name(){
		return $this->name;
	}
	
} 