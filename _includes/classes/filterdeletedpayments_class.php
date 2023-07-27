<?php

class filterdeletedpayments extends reportfilter{
	
	const DELETED_DEALS = 'deleted';
	const DELETED_DEALS_COLUMN = 'pp.deleted';
	
	const ONLY_DELETED = 1;
	const ONLY_NOT_DELETED = 0;
	const ALL_DEALS = 2;
	
	private $filter_deleted;
	private $names;
	
	public function __construct($hidden = false){
		parent::__construct($hidden);
		$this->filter_deleted = self::ONLY_NOT_DELETED;
		$this->names = array(self::ONLY_DELETED=>'Deleted Payments',
				self::ONLY_NOT_DELETED=>'Active Payments',
				self::ALL_DEALS=>'All Payments');
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
		$form->add_hidden(self::DELETED_DEALS, $this->filter_deleted);
	}
	
	private function build_visible(formbuilder &$form){
		foreach($this->names as $id=>$val){
			$options[] = array('id'=>$id, 'val'=>$val);
		}
		$form->add_select(self::DELETED_DEALS, $options, 'Include Deleted', $this->filter_deleted, $this->names[$this->filter_deleted]);
	}
	
	public function handle(&$array){
		if(isset($array[self::DELETED_DEALS])){
			switch ($array[self::DELETED_DEALS]){
				case self::ONLY_DELETED:
				case self::ONLY_NOT_DELETED:
				case self::ALL_DEALS:
					$this->filter_deleted = $array[self::DELETED_DEALS];
				break;				
			}
		}
	}
	
	public function wheres(reportwherebuilder &$wherebuilder){
		if($this->filter_deleted != self::ALL_DEALS){
			$wherebuilder->add_where_equal(self::DELETED_DEALS_COLUMN, $this->filter_deleted);
		}
	}
	
	public function get_active_vars(){
		if(!$this->invisible){
			return array(self::DELETED_DEALS=>$this->filter_deleted);
		}
	}
} 