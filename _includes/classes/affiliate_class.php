<?php

class affiliate{


	/*
	 * ------------------------------------------------------------
	 * VARIABLES
	 * ------------------------------------------------------------
	 */
	
	private $id;
	private $company_id;
	private $affiliate;
	private $active;
	 
	const TABLE = 'affiliate';
	
	const ACTIVE = 1;
	const INACTIVE = 0;
	 
	 
	/*
	 * ------------------------------------------------------------
	 * CONSTRUCTOR
	 * ------------------------------------------------------------
	 */
	
	public function __construct(){
		$this->zero_it();
	}
	 
	 
	/*
	 * ------------------------------------------------------------
	 * ZERO IT
	 * ------------------------------------------------------------
	 */
	 
	private function zero_it(){
		$this->id = 0;
		$this->company_id = 0;
		$this->affiliate = "";
		$this->active = self::INACTIVE;
	}
	 
	/*
	 * ------------------------------------------------------------
	 * POPULATE
	 * ------------------------------------------------------------
	 */
	 

	private function populate(){
		if(empty($this->id)) {$this->zero_it(); return false;}

		$where = array('id' => $this->id);
		$data = db_select_one(self::TABLE, $where);

		if(!$data){ $this->zero_it(); return false; }

		$this->set_id($data['id']);
		$this->set_company_id($data['company_id']);
		$this->set_affiliate($data['affiliate']);
		$this->set_active($data['active']);
		
		return true;
	}
	 

	/*
	 * ------------------------------------------------------------
	 * MAKE_ME_INTO
	 * ------------------------------------------------------------
	 */
	 

	public function make_me_into($v){
		$this->set_id($v);
		return $this->populate();
	}
	 
	/*
	 * ------------------------------------------------------------
	 * ACCESSORS
	 * ------------------------------------------------------------
	 */
	 

	public function get_id(){
		return $this->id;
	}

	public function get_company_id(){
		return $this->company_id;
	}
	public function get_affiliate(){
		return $this->affiliate;
	}
	public function get_active(){
		return $this->active;
	}

	/*
	 * ------------------------------------------------------------
	 * MODIFIERS
	 * ------------------------------------------------------------
	 */

	public function set_id($v){
		if($v){
			$this->id = $v;
		}
	}
	
	public function set_company_id($v){
		if($v){
			$this->company_id = $v;
		}
	}
	
	public function set_affiliate($v){
		if($v){
			$this->affiliate = $v;
		}
	}

	public function set_active($v){
		if($v){
			$this->active = self::ACTIVE;
		} else {
			$this->active = self::INACTIVE;
		}
	}
	
	public function toggle(){
		$this->set_active(!($this->get_active()));
	}
	 
	/*
	 * ------------------------------------------------------------
	 * BUILD_FORM
	 * ------------------------------------------------------------
	 */
	 
	 
	 
	 
	/*
	 * ------------------------------------------------------------
	 * TO_ARRAY
	 * ------------------------------------------------------------
	 */

	public function to_array(){
		$myarray = array();
		$myarray['id'] = $this->id;
		$myarray['company_id'] = $this->company_id;
		$myarray['affiliate'] = $this->affiliate;
		$myarray['active'] = $this->active;
		return $myarray;
	}
	
	
	/*
	 * ------------------------------------------------------------
	 * FROM_ARRAY
	 * ------------------------------------------------------------
	 */

	public function from_array(&$data){
		$this->set_id($data['id']);
		$this->set_name($data['company_id']);
		$this->set_username($data['affiliate']);
		$this->set_active($data['active']);
	}

	public function insert_new(){
		$data = $this->to_array();
		unset($data['id']);
		$result = db_insert_assoc_one(self::TABLE, $data);
		
		if($result){
			$this->set_id(db_get_insert_id());
		}
		
		return $result;
	}
	 
	/*
	 * ------------------------------------------------------------
	 * UPDATE_ONE
	 * ------------------------------------------------------------
	 */
	 

	public function update_one(){
		$where = array('id' => $this->id);
		$data = $this->to_array();
		unset($data['id']);
		
		$result = db_update(self::TABLE, $data, $where);
		
		return $result;
	}
	/* 
	 *-------------------------------------------------------------
	 * DELETE_ONE
	 *-------------------------------------------------------------
	 */
	public function delete_one() {
		$sql = "DELETE from " . self::TABLE . " WHERE id='" . $this->get_id() . "' ";
		db_delete_bare($sql);//yes, terrible, I know
		
		$affiliates = db_select(self::TABLE, array('company_id'=>$this->get_company_id()));
		$aff_ids = array();
		foreach($affiliates as $row) {
			$aff_ids[] = $row['affiliate'];
		}
		$users = db_select('users', array('company_id'=>$this->get_company_id()));
		$user_class = new user();
		foreach($users as $user) {
			$aff = json_decode($user['affiliation'], true);
			
			if(is_array($aff)){
				$res = array_intersect($aff_ids, $aff);
				if(!is_array($res)) $res = array();
				$res = array_values($res);
				
				$user_class->make_me_into($user['id']);
				$user_class->set_affiliation($res);
				$user_class->update_one();
			}
		}
		return true;
	}
	 	
	/*
	 * ------------------------------------------------------------
	 * PREPARE NEW
	 * ------------------------------------------------------------
	 */
	public function prepare_new(){
		//Don't think there is anything that I need to add for this in company
		
	}
	 
	  	
	/*
	 * ------------------------------------------------------------
	 * PUBLIC MEMBER FUNCTIONS
	 * ------------------------------------------------------------
	 */
	 
	 
	 	
	/*
	 * ------------------------------------------------------------
	 * PRIVATE MEMBER FUNCTIONS
	 * ------------------------------------------------------------
	 */
	 
	 
	 	
	/*
	 * ------------------------------------------------------------
	 * PUBLIC STATIC FUNCTIONS
	 * ------------------------------------------------------------
	 */
	 
	public static function get_affiliates(){
		$data = db_select(self::TABLE, 1, '');
		
		return $data;
	}
	
	public static function get_active_affiliates(){
		$where = array('active'=>self::ACTIVE);
		$data = db_select(self::TABLE, $where, '');
		
		return $data;
	}

	public static function get_affiliates_by_company($company_id = ""){
		$where = array('company_id'=>$company_id);
		$data = db_select(self::TABLE, $where, '');
		
		return $data;
	}

	/*
	 * ------------------------------------------------------------
	 * PRIVATE STATIC FUNCTIONS
	 * ------------------------------------------------------------
	 */
	 	
	
	
	
}