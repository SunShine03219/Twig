<?php
class filterapproved extends reportfilter{
	
	const APPROVED = 'approved';
	const APPROVED_COLUMN = 'd.approved';
	
	const ONLY_APPROVED = 1;
	const ONLY_UNAPPROVED = 0;
	const ALL_DEALS = 2;
	
	private $filter_approved;
	private $names;
	
	public function __construct($hidden = true){
		parent::__construct($hidden);
		$this->filter_approved = self::ALL_DEALS;
		$this->names = array(self::ONLY_APPROVED=>'Approved Deals',
				self::ONLY_UNAPPROVED=>'Unapproved Deals',
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
		$form->add_hidden(self::APPROVED, $this->filter_approved);
	}
	
	private function build_visible(formbuilder &$form){
		foreach($this->names as $id=>$val){
			$options[] = array('id'=>$id, 'val'=>$val);
		}
		$form->add_select(self::APPROVED, $options, 'Approved Status', $this->filter_approved, $this->names[$this->filter_approved]);
	}
	
	public function handle(&$array){
		if(isset($array[self::APPROVED])){
			switch ($array[self::APPROVED]){
				case self::ONLY_APPROVED:
				case self::ONLY_UNAPPROVED:
				case self::ALL_DEALS:
					$this->filter_approved =$array[self::APPROVED];
				break;
			}
		}
	}
	
	public function wheres(reportwherebuilder &$wherebuilder){
		if($this->filter_approved != self::ALL_DEALS){
			$wherebuilder->add_where_equal(self::APPROVED_COLUMN, $this->filter_approved);
		}
	}
	
	public function get_active_vars(){
		if(!$this->invisible){
			return array(self::APPROVED=>$this->filter_approved);
		}
	}
}