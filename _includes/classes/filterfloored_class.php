<?php
class filterfloored extends reportfilter{
	
	const FLOORED = 'floored';
	const FLOORED_COLUMN = 'd.flooring_id';
	
	const ONLY_FLOORED = 1;
	const ONLY_UNFLOORED = 0;
	const ALL_DEALS = 2;
	
	private $filter_floored;
	private $names;
	
	public function __construct($hidden = false){
		parent::__construct($hidden);
		$this->filter_floored = self::ONLY_FLOORED;
		$this->names = array(self::ONLY_UNFLOORED=>'Non-Floored',
				self::ONLY_FLOORED=>'Floored',
				self::ALL_DEALS=>'All Deals');
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
		$form->add_hidden(self::FLOORED, $this->filter_floored);
	}
	
	private function build_visible(formbuilder &$form){
		foreach($this->names as $id=>$val){
			$options[] = array('id'=>$id, 'val'=>$val);
		}
		$form->add_select(self::FLOORED,$options,'Floored', $this->filter_floored, $this->names[$this->filter_floored]);
	}
	
	public function handle(&$array){
		if(isset($array[self::FLOORED])){
			switch($array[self::FLOORED]){
				case self::ONLY_UNFLOORED:
				case self::ONLY_FLOORED:
				case self::ALL_DEALS:
					$this->filter_floored = $array[self::FLOORED];
				break;
			}
		}
	}
	
	public function wheres(reportwherebuilder &$wherebuilder){
		if($this->filter_floored != self::ALL_DEALS){
			if($this->filter_floored == self::ONLY_FLOORED){
				$wherebuilder->add_where_greater(self::FLOORED_COLUMN, 0);
			} else {
				$wherebuilder->add_where_equal(self::FLOORED_COLUMN, 0);
			}
		}
	}
	
	public function get_active_vars(){
		if(!$this->invisible){
			return array(self::FLOORED=>$this->filter_floored);
		}
	}
}