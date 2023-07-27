<?php
class filtersearch extends reportfilter{
	
	const SEARCH = 'search';
	//const SEARCH_COLUMN = 'composite';
	const SEARCH_COLUMN = 'LOWER(CONCAT(client_name,stock,convert(year,char(4)),make,model))';

	private $fields;
	private $incoming;
	
	
	public function __construct($hidden=true){
		parent::__construct($hidden);
		$this->fields = array();
		$this->incoming = '';
	}
	
	public function build(formbuilder &$form){
		//filtersearch doesn't show up in customizer
		$form->add_hidden('search', $this->incoming);
	}
	
	public function handle(&$array){
		if(isset($array[self::SEARCH])){
			$this->split_incoming($array[self::SEARCH]);
		}
	}
	
	private function split_incoming($incoming){
		$texts = preg_split('/\s/', $incoming);
		$this->fields = array_map('strtolower', $texts);
		$this->incoming = join(' ', $texts);
	}
	
	public function wheres(reportwherebuilder &$wherebuilder){
		if(!empty($this->fields)){
			$formatted = array();
			foreach($this->fields as $field){
				$formatted[] = self::SEARCH_COLUMN . ' LIKE "%'.$field.'%"';
			}
			$query = '(' . join(' AND ', $formatted) . ')';
			$wherebuilder->add_custom($query);
		} else {
			$wherebuilder->add_where_equal(1, 2);
		}
	}
	
	public function get_active_vars(){
		if(!$this->invisible){
			return array(self::SEARCH=>$this->incoming);
		}
	}
};