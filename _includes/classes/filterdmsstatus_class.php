<?php
class filterdmsstatus extends reportfilter{
	
	const DMS_STATUS = 'dmsstatus';
	const DMS_STATUS_COLUMN = 'd.closed_dms';
	
	const ONLY_CLOSED = 1;
	const ONLY_OPEN = 0;
	const ALL_DEALS = 2;
	
	private $filter_dms_status;
	private $names;
	
	public function __construct($hidden = true){
		parent::__construct($hidden);
		$this->filter_dms_status = self::ONLY_CLOSED;
		$this->names = array(self::ONLY_OPEN=>'Open Deals',
				self::ONLY_CLOSED=>'Closed Deals',
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
		$form->add_hidden(self::DMS_STATUS, $this->filter_dms_status);
	}
	
	private function build_visible(formbuilder &$form){
		foreach($this->names as $id=>$val){
			$options[] = array('id'=>$id, 'val'=>$val);
		}
		$form->add_select(self::DMS_STATUS, $options, 'DMS Status', $this->filter_dms_status, $this->names[$this->filter_dms_status]);
	}
	
	public function handle(&$array){
		if(isset($array[self::DMS_STATUS])){
			switch ($array[self::DMS_STATUS]){
				case self::ONLY_OPEN:
				case self::ONLY_CLOSED:
				case self::ALL_DEALS:
					$this->filter_dms_status =$array[self::DMS_STATUS];
				break;
			}
		}
	}
	
	public function wheres(reportwherebuilder &$wherebuilder){
		if($this->filter_dms_status != self::ALL_DEALS){
			$wherebuilder->add_where_equal(self::DMS_STATUS_COLUMN, $this->filter_dms_status);
		}
	}
	
	public function get_active_vars(){
		if(!$this->invisible){
			return array(self::DMS_STATUS=>$this->filter_dms_status);
		}
	}
}