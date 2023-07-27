<?php

class sales extends toggle{
	
	const TABLE = 'salespeople';
	const PRETTY_NAME = 'Salespeople';
	
	public function __construct(){
		parent::__construct();
		$this->table = self::TABLE;
	}
	
	public function formbuilder_quick_insert(formbuilder &$form){
		return parent::formbuilder_quick_insert($form, 'New Salesperson');
	}
	
	public function formbuilder_new(formbuilder &$form){
		return parent::formbuilder_new($form, 'Create Sales Person');
	}
	
	public function formbuilder_edit(formbuilder &$form){
		return parent::formbuilder_edit($form, 'Edit Sales Person');
	}
	
	public static function get_salespeople($company_id, $active = ""){
		return parent::get_list(self::TABLE, $company_id, $active);
	}
	
	public static function get_active_salespeople($company_id){
		return self::get_salespeople($company_id, 1);
	}
	
	public static function get_salesperson_name($id){
		return parent::get_single_name(self::TABLE, $id);
	}
	
	public static function get_salespeople_names(&$ids){
		return parent::get_multiple_names(self::TABLE, $ids);
	}
	
	public function get_label(){
		return 'Salesperson';
	}

	
	public function validate_new($data){
		return parent::validate_new($data);
	}

	public function insert_new(){
		return parent::insert_new();
	}
}