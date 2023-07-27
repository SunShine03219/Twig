<?php

class lead extends toggle{
	
	const TABLE = 'lead';
	const PRETTY_NAME = 'Lead Source';
	
	public function __construct(){
		parent::__construct();
		$this->table = self::TABLE;
	}
	
	public function formbuilder_quick_insert(formbuilder &$form){
		return parent::formbuilder_quick_insert($form, 'New Lead Source');
	}
	
	public function formbuilder_new(formbuilder &$form){
		return parent::formbuilder_new($form, 'Create Lead Source');
	}
	
	public function formbuilder_edit(formbuilder &$form){
		return parent::formbuilder_edit($form, 'Edit Lead Source');
	}
	
	public static function get_lead_companies($company_id, $active = ""){
		return parent::get_list(self::TABLE, $company_id, $active);
	}
	
	public static function get_active_lead_companies($company_id){
		return self::get_lead_companies($company_id, 1);
	}
	
	public static function get_lead_name($id){
		return parent::get_single_name(self::TABLE, $id);
	}
	
	public function get_label(){
		return 'Lead Source';
	}

	
	public function validate_new($data){
		return parent::validate_new($data);
	}

	public function insert_new(){
		return parent::insert_new();
	}
}