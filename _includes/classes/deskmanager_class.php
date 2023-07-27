<?php

class deskmanager extends toggle{
	
	const TABLE = 'deskmanagers';
	const PRETTY_NAME = 'Desk Managers';
	
	public function __construct(){
		parent::__construct();
		$this->table = self::TABLE;
	}
	
	public function formbuilder_quick_insert(formbuilder &$form){
		return parent::formbuilder_quick_insert($form, 'New Desk Manager');
	}
	
	public function formbuilder_new(formbuilder &$form){
		return parent::formbuilder_new($form, 'Create Desk Manager');
	}
	
	public function formbuilder_edit(formbuilder &$form){
		return parent::formbuilder_edit($form, 'Edit Desk Manager');
	}
	
	public static function get_deskmanagers($company_id, $active = ""){
		return parent::get_list(self::TABLE, $company_id, $active);
	}
	
	public static function get_active_deskmanagers($company_id){
		return self::get_deskmanagers($company_id, 1);
	}
	
	public static function get_deskmanager_name($id){
		return parent::get_single_name(self::TABLE, $id);
	}
	
	public function get_label(){
		return 'Desk Manager';
	}

	public function validate_new($data){
		return parent::validate_new($data);
	}

	public function insert_new(){
		return parent::insert_new();
	}
}