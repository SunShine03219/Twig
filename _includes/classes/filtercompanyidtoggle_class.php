<?php

class filtercompanyidtoggle extends reportfilter{

	const COMPANY_ID = 'companyid';
	const COMPANY_ID_COLUMN = 't.company_id';

	private $filter_company_id;

	public function __construct($hidden = true, $invisible = true){
		parent::__construct($hidden, $invisible);
	}

	public function build(formbuilder &$form){
		if(!$this->invisible){
			if(!empty($this->filter_company_id)){
				if($this->hidden){
					$this->build_hidden($form);
				} else {
					$form->start_section('form_row');
					$this->build_visible($form);
					$form->end_section();
				}
			}
		}
	}

	private function build_hidden(formbuilder &$form){
		$form->add_hidden(self::COMPANY_ID, $this->filter_company_id);
	}

	private function build_visible(formbuilder &$form){
		throw new exception ('Visible company id filter not implemented');
	}

	public function handle(&$array){
		if(isset($array[self::COMPANY_ID])){
			$this->filter_company_id = intval($array[self::COMPANY_ID]);
		}
	}

	public function wheres(reportwherebuilder &$wherebuilder){
		if(!empty($this->filter_company_id)){
			$wherebuilder->add_where_equal(self::COMPANY_ID_COLUMN, $this->filter_company_id);
		}
	}
	
	public function get_active_vars(){
		if(!$this->invisible){
			return array(self::COMPANY_ID=>$this->filter_company_id);
		}
	}
}