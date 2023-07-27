<?php

class finance extends toggle{
	
	const TABLE = 'financepeople';
	const PRETTY_NAME = 'Finance People';
	
	public function __construct(){
		parent::__construct();
		$this->table = self::TABLE;
	}

	public function formbuilder_quick_insert(formbuilder &$form){
		return parent::formbuilder_quick_insert($form, 'New Finance Person');
	}
	
	public function formbuilder_new(formbuilder &$form){
		return parent::formbuilder_new($form, 'Create Finance Person');
	}
	
	public function formbuilder_edit(formbuilder &$form){
		return parent::formbuilder_edit($form, 'Edit Finance Person');
	}
	
	public static function get_financepeople($company_id, $active = ""){
		return parent::get_list(self::TABLE, $company_id, $active);
	}
	
	public static function get_active_financepeople($company_id){
		return self::get_financepeople($company_id, 1);
	}
	public static function get_active_financepayment($company_id){
		return array(
                    array('id'=>1,'val'=>'Yes'),
                    array('id'=>0,'val'=>'No')
                );
	}
	
	public static function get_financeperson_name($id){
		return parent::get_single_name(self::TABLE, $id);
	}
	
	public function get_label(){
		return 'Finance Person';
	}
	
	public function validate_new($data){
		return parent::validate_new($data);
	}

	public function insert_new(){
		return parent::insert_new();
	}
}
