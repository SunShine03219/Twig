<?php

class lender extends toggle{
	
	/*private $id;
	private $company_id;
	private $name;
	private $active;*/
	private $contact;
	private $phone_contact;
	private $phone_fax;
	private $phone_main;
	private $website;
	private $address1;
	private $address2;
	private $city;
	private $state;
	private $zip;
	
	const TABLE = 'lenders';
	const PRETTY_NAME = 'Lenders';
	
	const ACTIVE = 1;
	const INACTIVE = 0;
	
	
   /*
	* ------------------------------------------------------------
	* CONSTRUCTOR
	* ------------------------------------------------------------
	*/
	
	public function __construct(){
		parent::__construct();
		$this->zero_it();
		$this->table = self::TABLE;
	}
	
	
	/*
	 * ------------------------------------------------------------
	* ZERO IT
	* ------------------------------------------------------------
	*/
	private function zero_it(){
		$this->contact = '';
		$this->address1 = '';
		$this->address2 = '';
		$this->city = '';
		$this->state = '';
		$this->zip = '';
		$this->phone_main = '';
		$this->phone_contact = '';
		$this->phone_fax = '';
		$this->website = '';
	}
	
	/*
	 * ------------------------------------------------------------
	* POPULATE
	* ------------------------------------------------------------
	*/
	
	protected function populate($data){
		parent::populate($data);
		$this->set_contact($data['contact']);
		$this->set_phone_main($data['phone_main']);
		$this->set_phone_contact($data['phone_contact']);
		$this->set_phone_fax($data['phone_fax']);
		$this->set_website($data['website']);
		$this->set_address1($data['address1']);
		$this->set_address2($data['address2']);
		$this->set_city($data['city']);
		$this->set_state($data['state']);
		$this->set_zip($data['zip']);
	}
	
	
	
	
	/*
	 * ------------------------------------------------------------
	* ACCESSORS
	* ------------------------------------------------------------
	*/

	
	public function get_contact(){
		return $this->contact;
	}
	
	public function get_address1(){
		return $this->address1;
	}
	
	public function get_address2(){
		return $this->address2;
	}
	
	public function get_city(){
		return $this->city;
	}
	
	public function get_state(){
		return $this->state;
	}
	
	public function get_zip(){
		return $this->zip;
	}
	
	public function get_phone_main(){
		return $this->phone_main;
	}
	
	public function get_phone_contact(){
		return $this->phone_contact;
	}
	
	public function get_phone_fax(){
		return $this->phone_fax;
	}
	
	public function get_website(){
		return $this->website;
	}
	
	
	
	/*
	 * ------------------------------------------------------------
	* MODIFIERS
	* ------------------------------------------------------------
	*/
	public function set_contact($v){
		if($v){
			$this->contact = $v;
		}
	}
	
	public function set_address1($v){
		$this->address1 = $v;
	}
	
	public function set_address2($v){
		$this->address2 = $v;
	}
	
	public function set_city($v){
		$this->city = $v;
	}
	
	public function set_state($v){
		$this->state = $v;
	}
	
	public function set_zip($v){
		$this->zip = $v;
	}
	
	public function set_phone_main($v){
		if($v){
			$this->phone_main = $v;
		}
	}
	
	public function set_phone_contact($v){
		if($v){
			$this->phone_contact = $v;
		}
	}
	
	public function set_phone_fax($v){
		$this->phone_fax = $v;
	}
	
	public function set_website($v){
		$this->website = $v;
	}
	
	public function set_active($v){
		if($v){
			$this->active = self::ACTIVE;
		} else {
			$this->active = self::INACTIVE;
		}
	}
	
	/*
	 * ------------------------------------------------------------
	* TO_ARRAY
	* ------------------------------------------------------------
	*/
	
	public function to_array(){
		$myarray = parent::to_array();
		$myarray['contact'] = $this->contact;
		$myarray['address1'] = $this->address1;
		$myarray['address2'] = $this->address2;
		$myarray['city'] = $this->city;
		$myarray['state'] = $this->state;
		$myarray['zip'] = $this->zip;
		$myarray['phone_main'] = $this->phone_main;
		$myarray['phone_contact'] = $this->phone_contact;
		$myarray['phone_fax'] = $this->phone_fax;
		$myarray['website'] = $this->website;
		return $myarray;
	}
	
	
	/*
	 * ------------------------------------------------------------
	* FROM_ARRAY
	* ------------------------------------------------------------
	*/
	
	public function from_array($data){
		parent::from_array($data);
		$this->set_contact($data['contact']);
		$this->set_address1($data['address1']);
		$this->set_address2($data['address2']);
		$this->set_city($data['city']);
		$this->set_state($data['state']);
		$this->set_zip($data['zip']);
		$this->set_phone_main($data['phone_main']);
		$this->set_phone_contact($data['phone_contact']);
		$this->set_phone_fax($data['phone_fax']);
		$this->set_website($data['website']);
	}
	
	
	/*
	 * ------------------------------------------------------------
	* VALIDATE_EDIT
	* ------------------------------------------------------------
	*/
	
	public function validate_edit($data){
		$error_flag = false;
		
		if(!parent::validate_edit($data)){
			$error_flag = true;;
		}
		
		$this->set_contact($data['contact']);
		$this->set_address1($data['address1']);
		$this->set_address2($data['address2']);
		$this->set_city($data['city']);
		$this->set_state($data['state']);
		$this->set_zip($data['zip']);
		$this->set_phone_main($data['phone_main']);
		$this->set_phone_contact($data['phone_contact']);
		$this->set_phone_fax($data['phone_fax']);
		$this->set_website($data['website']);
		
		if($error_flag){
			set_postback_msg('Invalid Data - please review your entries below');
		}
		
		return !$error_flag;
	}
	
	public function toggle_me(){
		$this->set_active(!$this->get_active());
	}
	
	/*
	 * ------------------------------------------------------------
	* Formbuilders - public
	* ------------------------------------------------------------
	*/
	
	public function formbuilder_quick_insert(formbuilder &$form){
		$form->start_section("form_row");
		$form->add_subtitle('New Lender');
		$form->end_section();
		
		$form->start_section("form_row");
		$this->formbuilder_name($form);
		$form->add_inline_submit();
		$form->end_section();
	}
	
	public function formbuilder_new(formbuilder &$form){
		$form->start_section("form_row");
		$form->add_subtitle('Create Lender');
		$form->end_section();
		
		$this->formbuilder_toggle($form);
		
		$form->add_submit();
	}
	
	public function formbuilder_edit(formbuilder &$form){
		$form->start_section("form_row");
		$form->add_subtitle('Edit Lender');
		$form->end_section();
		
		$this->formbuilder_toggle($form);
		
		$this->formbuilder_lender_contact($form);
		
		$form->add_submit();
	}
	
	/*
	 * ------------------------------------------------------------
	* Formbuilders - private
	* ------------------------------------------------------------
	*/
	
	protected function formbuilder_lender_contact(formbuilder &$form){
		$form->start_section('form_row');
		$form->add_subtitle('Contact Information');
		$form->end_section();
		$form->start_section('form_row');
		$this->formbuilder_contact($form);
		$this->formbuilder_phone_contact($form);
		$form->end_section();
		$form->start_section('form_row');
		$this->formbuilder_phone_main($form);
		$this->formbuilder_phone_fax($form);
		$this->formbuilder_website($form);
		$form->end_section();
		$form->start_section('form_row');
		$form->add_subtitle("Address");
		$form->end_section();
		$form->start_section('form_row');
		$this->formbuilder_address1($form);
		$form->end_section();
		$form->start_section('form_row');
		$this->formbuilder_address2($form);
		$form->end_section();
		$form->start_section('form_row');
		$this->formbuilder_city($form);
		$form->end_section();
		$form->start_section('form_row');
		$this->formbuilder_state($form);
		$form->end_section();
		$form->start_section('form_row');
		$this->formbuilder_zip($form);
		$form->end_section();
	}
	
	private function formbuilder_id_hidden(formbuilder &$form){
		$form->add_hidden('id', $this->get_id());
	}
	
	private function formbuilder_name(formbuilder &$form, $editable = 1){
		$form->add_text('name', 'Name', $this->get_name(), 1, $editable);
	}
	
	private function formbuilder_company_id_hidden(formbuilder &$form, $value=""){
		$form->add_hidden('company_id', ($value!=""?$value:$this->get_company_id()));
	}
	
	private function formbuilder_active(formbuilder &$form, $disabled = 0){
		$form->add_checkbox('active', 'active', 'Active', $this->get_active(), 0, $disabled);
	}
	
	private function formbuilder_contact(&$form, $editable = 1){
		$form->add_text('contact', 'Main Contact', $this->get_contact(), 0, $editable);
	}
	
	private function formbuilder_address1(&$form, $editable = 1){
		$form->add_text('address1', 'Address 1', $this->get_address1(), 0, $editable);
	}
	
	private function formbuilder_address2(&$form, $editable = 1){
		$form->add_text('address2', 'Address 2', $this->get_address2(), 0, $editable);
	}
	
	private function formbuilder_city(&$form, $editable = 1){
		$form->add_text('city', 'City', $this->get_city(), 0, $editable);
	}
	
	private function formbuilder_state(&$form, $editable = 1){
		$form->add_text('state', 'State', $this->get_state(), 0, $editable);
	}
	
	private function formbuilder_zip(&$form, $editable = 1){
		$form->add_text('zip', 'Zip Code', $this->get_zip(), 0, $editable);
	}
	
	private function formbuilder_phone_main(&$form, $editable = 1){
		$form->add_text('phone_main', 'Main Phone', $this->get_phone_main(), 0, $editable);
	}
	
	private function formbuilder_phone_contact(&$form, $editable = 1){
		$form->add_text('phone_contact', 'Contact Phone', $this->get_phone_contact(), 0, $editable);
	}
	
	private function formbuilder_phone_fax(&$form, $editable = 1){
		$form->add_text('phone_fax', 'Fax Number', $this->get_phone_fax(), 0, $editable);
	}
	
	private function formbuilder_website(&$form, $editable = 1){
		$form->add_text('website', 'Website', $this->get_website(), 0, $editable);
	}
	
	public static function get_lenders($company_id, $active = ""){
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
		
		$data = db_select(self::TABLE, $where, '', $columns, '', $order_by);
		return $data;
	}
	
	public static function get_active_lenders($company_id){
		return self::get_lenders($company_id, 1);
	}
	
	public static function get_lender_name($id){
		$where = array('id' => $id);
		$columns = array('name');
		$data = db_select_one(self::TABLE, $where, '', $columns);
		if(!$data){
			return "id $id not found";
		} else {
		return $data['name'];
		}
	}
	
	public function get_label(){
		return 'Lending Source';
	}
	
	public function validate_new($data){
		$error_flag = false;
		
		if(!parent::validate_new($data)){
			$error_flag = true;;
		}
		
		$this->set_contact($data['contact']);
		$this->set_address1($data['address1']);
		$this->set_address2($data['address2']);
		$this->set_city($data['city']);
		$this->set_state($data['state']);
		$this->set_zip($data['zip']);
		$this->set_phone_main($data['phone_main']);
		$this->set_phone_contact($data['phone_contact']);
		$this->set_phone_fax($data['phone_fax']);
		$this->set_website($data['website']);
		
		if($error_flag){
			set_postback_msg('Invalid Data - please review your entries below');
		}
		
		return !$error_flag;
		//return parent::validate_new($data);
	}

	public function insert_new(){
		return parent::insert_new();
	}
}