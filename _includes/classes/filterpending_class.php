<?php
class filterpending extends reportfilter{
	
	const PENDING = 'pending';
	const PENDING_COLUMN = 'd.pending_documents';
	
	const ONLY_PENDING = 1;
	const ONLY_UNPENDING = 0;
	const ALL_DEALS = 2;
	
	private $filter_pending;
	private $names;
	
	public function __construct($hidden = true){
		parent::__construct($hidden);
		$this->filter_pending = self::ALL_DEALS;
		$this->names = array(self::ONLY_PENDING=>'Pending Documents Deals',
				self::ONLY_UNPENDING=>'Unpending Document Deals',
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
		$form->add_hidden(self::PENDING, $this->filter_pending);
	}
	
	private function build_visible(formbuilder &$form){
		foreach($this->names as $id=>$val){
			$options[] = array('id'=>$id, 'val'=>$val);
		}
		$form->add_select(self::PENDING, $options, 'Approved Status', $this->filter_pending, $this->names[$this->filter_pending]);
	}
	
	public function handle(&$array){
		if(isset($array[self::PENDING])){
			switch ($array[self::PENDING]){
				case self::ONLY_PENDING:
				case self::ONLY_UNPENDING:
				case self::ALL_DEALS:
					$this->filter_pending =$array[self::PENDING];
				break;
			}
		}
	}
	
	public function wheres(reportwherebuilder &$wherebuilder){
		if($this->filter_pending != self::ALL_DEALS){
			$wherebuilder->add_where_equal(self::PENDING_COLUMN, $this->filter_pending);
		}
	}
	
	public function get_active_vars(){
		if(!$this->invisible){
			return array(self::PENDING=>$this->filter_pending);
		}
	}
}