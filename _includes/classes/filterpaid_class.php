<?php
class filterpaid extends reportfilter{
	
	const PAID = 'paid';
	const PAID_COLUMN = 'paid';
	
	const ONLY_PAID = 1;
	const ONLY_UNPAID = 0;
	const ALL_PAYMENTS = 2;
	
	private $filter_paid;
	private $names;
	
	public function __construct($hidden = false){
		parent::__construct($hidden);
		$this->filter_paid = self::ALL_PAYMENTS;
		$this->names = array(
				self::ONLY_UNPAID=>'Unpaid',
				self::ONLY_PAID=>'Paid',
				self::ALL_PAYMENTS=>'All'
				);
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
		$form->add_hidden(self::PAID, $this->filter_paid);
	}
	
	private function build_visible(formbuilder &$form){
		$options = array();
		foreach($this->names as $id=>$val){
			$options[] = array('id'=>$id, 'val'=>$val);
		}
		$form->add_select(self::PAID, $options, 'Payment Status', $this->filter_paid, $this->names[$this->filter_paid]);
	}
	
	public function handle(&$array){
		if(isset($array[self::PAID])){
			switch($array[self::PAID]){
				case self::ONLY_PAID:
				case self::ONLY_UNPAID:
				case self::ALL_PAYMENTS:
					$this->filter_paid = $array[self::PAID];
				break;
			}
		}
	}
	
	public function wheres(reportwherebuilder &$wherebuilder){
		if($this->filter_paid != self::ALL_PAYMENTS){
			$wherebuilder->add_where_equal(self::PAID_COLUMN, $this->filter_paid);
		}
	}
	
	public function get_active_vars(){
		if(!$this->invisible){
			return array(self::PAID=>$this->filter_paid);
		}
	}
}