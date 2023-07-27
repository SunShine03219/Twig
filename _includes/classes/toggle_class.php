<?php

class toggle{
	
	protected $table;
	
	protected $id;
	protected $company_id;
	protected $active;
	protected $name;
	
	const ACTIVE = 1;
	const INACTIVE = 0;
	
	public function __construct(){
		$this->zero_it();
	}
	
	private function zero_it(){
		$this->id = 0;
		$this->company_id = 0;
		$this->name = "";
		$this->active = 0;
	}
	
	public function get_id(){
		return $this->id;
	}

	public function get_company_id(){
		return $this->company_id;
	}

	public function get_name(){
		return $this->name;
	}

	public function get_active(){
		return $this->active;
	}
	
	public function get_label(){
		return 'Toggleable Item';
	}
	
	public function set_table($v){
	    $this->table = $v;
	}
	
	public function set_id($v){
		if(is_numeric($v)){
			$this->id = $v;
		} else {
			$this->id = 0;
		}
	}

	public function set_company_id($v){
		if(is_numeric($v)){
			$this->company_id = $v;
		} else {
			$this->company_id = 0;
		}
	}

	public function set_name($v){
		if($v){
			$this->name = $v;
		} else {
			$this->name = "";
		}
	}

	public function set_active($v){
		$this->active = ($v?self::ACTIVE:self::INACTIVE);
	}
	
	public function is_active(){
		return ($this->active == self::ACTIVE);
	}
	
	public function toggle(){
		$this->set_active(!($this->is_active()));
	}
	
	/*
	 * ------------------------------------------------------------
	 * VALIDATION
	 * ------------------------------------------------------------
	 */
	
	public function validate_new($data){
		$error_flag = false;
		
		//test name
		if(empty($data['name'])){
			$error_flag = true;
			add_postback_error('name', 'Name cannot be blank');
		} else {
			$this->set_name(trim($data['name']));
		}
		
		
		//test company id
		if(empty($data['company_id'])){
			$error_flag = true;
			add_postback_error('company_id', 'Company ID must be set');
		} elseif(!db_exists(company::TABLE, 'id', $data['company_id'])){
			$error_flag = true;
			add_postback_error('company_id', "Company ID {$data['company_id']} doesn't exist.");
		} else {
			$this->set_company_id($data['company_id']);
		}
		
		//test name/company id combination doesn't exist
		if(!$error_flag){
			if(db_exists_multi($this->table, array('name'=>$this->name, 'company_id'=>$this->company_id))){
				$error_flag = true;
				add_postback_error('name', "{$data['name']} already exists for your company.");
			}
		}
		
		$this->set_active(self::ACTIVE);
		
		return !$error_flag;
	}
	
	public function validate_edit($data){
		$error_flag = false;
		
		//test name
		if($this->name != $data['name'] || $this->company_id != $data['company_id']){
			//name or company id has changed
			
			//test name
			if(empty($data['name'])){
				$error_flag = true;
				add_postback_error('name', 'Name cannot be blank');
			} else {
				$this->set_name($data['name']);
			}
			
			//test company_id
			if(empty($data['company_id'])){
				$error_flag = true;
				add_postback_error('company_id', 'Company ID must be set');
			} elseif(!db_exists(company::TABLE, 'id', $data['company_id'])){
				$error_flag = true;
				add_postback_error('company_id', "Company ID {$data['company_id']} doesn't exist.");
			} else {
				$this->set_company_id($data['company_id']);
			}
			
			//check if new name and company id combination already exists
			if(!$error_flag){
				if (db_exists_multi($this->table, array('name'=> $this->name, 'company_id' => $this->company_id))){
					$error_flag = true;
					add_postback_error('name', $data['name'] . ' already exists in the database for your company');
				}
			}
		}
		
		//change active as needed
		$this->set_active(isset($data['active'])?1:0);
			
		return !$error_flag;
	}
	
	
	/*
	 * ------------------------------------------------------------
	 * prep, insert, update
	 * ------------------------------------------------------------
	 */
	
	public function prepare_new(){
		$this->set_company_id($_SESSION['FT']['company_id']);
	}
	
	public function toggle_me(){
		$this->set_active(!$this->is_active());
	}
	
	public function insert_new(){
		if(empty($this->name) || empty($this->company_id)){
			set_postback_msg('Incomplete information for insert, name or company id is blank.');
			return false;
		}
		$data = $this->to_array();
		unset($data['id']);
		$result = db_insert_assoc_one($this->table, $data);
		
		if($result){
			$this->set_id(db_get_insert_id());
		}
		
		return $result;
	}
	
	public function update_one(){
		if(empty($this->id)){
			set_postback_msg('Incomplete information for Update, ID is blank');
			return false;
		}

		$where = array('id' => $this->id);
		$data = $this->to_array();
		unset($data['id']);
		
		$result = db_update($this->table, $data, $where);

		return $result;
	}
	
	public function to_array(){
		$myarray = array();
		$myarray['id'] = $this->id;
		$myarray['company_id'] = $this->company_id;
		$myarray['name'] = $this->name;
		$myarray['active'] = $this->active;
		return $myarray;
	}
	
	public function from_array($data){
		$this->set_id($data['id']);
		$this->set_company_id($data['company_id']);
		$this->set_name($data['name']);
		$this->set_active($data['active']);
	}
	
	public function make_me_into($id){
		$this->set_id($id);
		
		$data = $this->retrieve_data();
		
		if($data === false || empty($data)){
			return false;
		}
		
		$this->populate($data);
		
		return true;
	}
	
	protected function populate($data){
		
		$this->set_id($data['id']);
		$this->set_company_id($data['company_id']);
		$this->set_name($data['name']);
		$this->set_active($data['active']);
		
	}
	
	private function retrieve_data(){
		if(empty($this->id)){
			return false;
		}
		
		$where = array('id'=>$this->get_id());
		
		return db_select_one($this->table, $where);
	}
	
	/*
	 * ------------------------------------------------------------
	 * Formbuilders - public
	 * ------------------------------------------------------------
	 */
	
	public function formbuilder_new_modal(formbuilder &$form){
		$form->start_section('form_row');
		$form->add_subtitle('Add New ' . $this->get_label());
		$form->end_section();
		
		$form->start_section('form_row');
		$this->formbuilder_name($form);
		$form->end_section();
		
		$form->start_section('form_row');
		$form->add_submit();
		$form->end_section();
	}
	
	public function formbuilder_quick_insert(formbuilder &$form, $heading){
		$form->start_section("form_row");
			$form->add_subtitle($heading);
		$form->end_section();
		
		$form->start_section("form_row");
			$this->formbuilder_name($form);
			$form->add_inline_submit();
		$form->end_section();
	}

	public function formbuilder_new(formbuilder &$form, $heading){
		$form->start_section("form_row");
		$form->add_subtitle($heading);
		$form->end_section();
		
		$this->formbuilder_toggle($form);
		
		$form->add_submit();		
	}
	
	public function formbuilder_edit(formbuilder &$form, $heading){
		$form->start_section("form_row");
		$form->add_subtitle($heading);
		$form->end_section();
		
		$this->formbuilder_toggle($form);	
		
		$form->add_submit();
	}
	
	/*
	 * ------------------------------------------------------------
	 * Formbuilders - private
	 * ------------------------------------------------------------
	 */
	
	protected function formbuilder_toggle(formbuilder &$form){

		$form->start_section("form_row");
		$this->formbuilder_name($form);
		$form->end_section();
		
		$form->start_section("form_row");
		$this->formbuilder_active($form);
		$form->end_section();
	}
	
	private function formbuilder_id_hidden(&$form){
		$form->add_hidden('id', $this->get_id());
	}
	
	private function formbuilder_name(&$form, $editable = 1){
	 	$form->add_text('name', 'Name', $this->get_name(), 1, $editable);
	 }
	 
	private function formbuilder_company_id_hidden(&$form, $value=""){
		$form->add_hidden('company_id', ($value!=""?$value:$this->get_company_id()));
	}
	
	private function formbuilder_active(&$form, $disabled = 0){
		$form->add_checkbox('active', 'active', 'Active', $this->get_active(), 0, $disabled);
	}
	
	/*
	 * ------------------------------------------------------------
	 * PUBLIC STATIC FUNCTIONS
	 * ------------------------------------------------------------
	 */
	
	public static function get_list($table, $company_id, $active=""){
		if(!is_numeric($company_id)){
			$data = array(array('id'=>0, 'val'=>"No Company ID"));
			return $data;
		}
		
		$where = array('company_id'=>$company_id);
		if($active==1 || ($active==0 && $active != "")){
			$where['active'] = 1;
		}
		
		$columns = array('id', 'name val');
		$order_by = "ORDER BY name ASC";
		
		$data = db_select($table, $where, '', $columns, '', $order_by);
		return $data;
	}
	
	public static function get_active_list($table, $company_id){
		return self::get_list($table, $company_id, 1);
	}
	
	public static function get_single_name($table, $id){
		$where = array('id' => $id);
		$columns = array('name');
		$data = db_select_one($table, $where, '', $columns);
		if(!$data){
			return "id $id not found";
		} else {
			return $data['name'];
		}
	}
	
	public static function get_multiple_names($table, &$a){
		if(!is_array($a)){
			return array();
		}
		$where = array();
		foreach($a as $id){
			$where[] = "id='$id'";
		}
		$whereclause = ' WHERE ' . join(' OR ', $where);
		$sql = 'SELECT id, name FROM ' . $table . $whereclause;
		$result = db_select_assoc_array($sql);
		if(!is_array($result)){
			return array();
		}
		$names = array();
		foreach($result as $row){
			$names[$row['id']] = $row['name'];
		}
		return $names;
	}
	
	public static function get_type_whitelist(){
		return array('deskmanager', 'finance', 'flooring', 'lead', 'gap', 'lender', 'reconvendor', 'sales', 'warranty');
	}
	
}