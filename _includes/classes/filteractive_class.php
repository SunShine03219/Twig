<?php

class filteractive extends reportfilter{
	
	const ACTIVE_ITEMS = 'active';
	const ACTIVE_ITEMS_COLUMN = 'active';
	
	const ONLY_ACTIVE = 1;
	const ONLY_INACTIVE = 0;
	const ALL_ITEMS = 2;
	
	private $filter_active;
	private $names;
	
	public function __construct($hidden = false){
		parent::__construct($hidden);
		$this->names = array(self::ONLY_ACTIVE=>'Active',
				self::ONLY_INACTIVE=>'Inactive',
				self::ALL_ITEMS=>'All Items');
	}
	
	public function build(formbuilder &$form){
		if(!$this->invisible){
			if($this->hidden){
				$this->build_hidden($form);
			} else {
				$form->start_section('form_row');
				$this->build_visible($form);
				$form->end_section();
			}
		}
	}
	
	private function build_hidden(formbuilder &$form){
		$form->add_hidden(self::ACTIVE_ITEMS, $this->filter_active);
	}
	
	private function build_visible(formbuilder &$form){
		foreach($this->names as $id=>$val){
			$options[] = array('id'=>$id, 'val'=>$val);
		}
		$form->add_select(self::ACTIVE_ITEMS, $options, 'Active', $this->filter_active, $this->names[$this->filter_active]);
	}
	
	public function handle(&$array){
		if(isset($array[self::ACTIVE_ITEMS])){
			switch($array[self::ACTIVE_ITEMS]){
				case self::ONLY_ACTIVE:
				case self::ONLY_INACTIVE:
				case self::ALL_ITEMS:
					$this->filter_active = $array[self::ACTIVE_ITEMS];
					break;
			}
		}
	}
	
	public function wheres(reportwherebuilder &$wherebuilder){
		if($this->filter_active != self::ALL_ITEMS){
			$wherebuilder->add_where_equal(self::ACTIVE_ITEMS_COLUMN, $this->filter_active);
		}
	}
	
	public function get_active_vars(){
		if(!$this->invisible){
			return array(self::ACTIVE_ITEMS=>$this->filter_active);
		}
	}
}