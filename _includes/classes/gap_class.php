<?php

class gap extends toggle{
	
	const TABLE = 'gapproviders';
	const PRETTY_NAME = 'Gap Providers';
	
	public function __construct(){
		parent::__construct();
		$this->table = self::TABLE;
	}
	
	public function formbuilder_quick_insert(formbuilder &$form){
		return parent::formbuilder_quick_insert($form, 'New Gap Provider');
	}
	
	public function formbuilder_new(formbuilder &$form){
		return parent::formbuilder_new($form, 'Create Gap Provider');
	}
	
	public function formbuilder_edit(formbuilder &$form){
		return parent::formbuilder_edit($form, 'Edit Gap Provider');
	}
	
	public static function get_gap($company_id, $active = ""){
		return parent::get_list(self::TABLE, $company_id, $active);
	}
	
	public static function get_active_gap($company_id){
		return self::get_gap($company_id, 1);
	}
	
	public static function get_gap_name($id){
		return parent::get_single_name(self::TABLE, $id);
	}
	
	public function get_label(){
		return 'Gap Provider';
	}
	
	public function validate_new($data){
		return parent::validate_new($data);
	}

	public function insert_new(){
		return parent::insert_new();
	}
}