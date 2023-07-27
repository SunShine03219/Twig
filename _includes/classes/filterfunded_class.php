<?php
class filterfunded extends reportfilter{
	
	const FUNDED = 'funded';
	const FUNDED_COLUMN = 'd.funded';
	
	const ONLY_FUNDED = 1;
	const ONLY_UNFUNDED = 0;
	const ALL_DEALS = 2;
	
	private $filter_funded;
	private $names;
	
	public function __construct($hidden = true){
		parent::__construct($hidden);
		$this->filter_funded = self::ALL_DEALS;
		$this->names = array(self::ONLY_FUNDED=>'Funded Deals',
				self::ONLY_UNFUNDED=>'Unfunded Deals',
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
		$form->add_hidden(self::FUNDED, $this->filter_funded);
	}
	
	private function build_visible(formbuilder &$form){
		foreach($this->names as $id=>$val){
			$options[] = array('id'=>$id, 'val'=>$val);
		}
		$form->add_select(self::FUNDED, $options, 'Funded Status', $this->filter_funded, $this->names[$this->filter_funded]);
	}
	
	public function handle(&$array){
		if(isset($array[self::FUNDED])){
			switch ($array[self::FUNDED]){
				case self::ONLY_FUNDED:
				case self::ONLY_UNFUNDED:
				case self::ALL_DEALS:
					$this->filter_funded =$array[self::FUNDED];
				break;
			}
		}
	}
	
	public function wheres(reportwherebuilder &$wherebuilder){
		if($this->filter_funded != self::ALL_DEALS){
			$wherebuilder->add_where_equal(self::FUNDED_COLUMN, $this->filter_funded);
		}
	}
	
	public function get_active_vars(){
		if(!$this->invisible){
			return array(self::FUNDED=>$this->filter_funded);
		}
	}
}