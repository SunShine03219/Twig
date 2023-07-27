<?php

class company{


	/*
	 * ------------------------------------------------------------
	 * VARIABLES
	 * ------------------------------------------------------------
	 */
	
	private $id;
	private $name;
	private $contact;
	private $address1;
	private $address2;
	private $city;
	private $state;
	private $zip;
	private $phone_main;
	private $phone_contact;
	private $phone_fax;
	private $website;
	private $card_digit;
	private $card_type;
	private $subscription;
	private $referred_by;
	private $active;
	private $stripe_customer_id;
	 
	const TABLE = 'company';
	
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
		$this->name = "";
		$this->username = "";
		$this->last_login = "";
		$this->contact = "";
		$this->address1 = "";
		$this->address2 = "";
		$this->city = "";
		$this->state = "";
		$this->zip = "";
		$this->phone_main = "";
		$this->phone_contact = "";
		$this->phone_fax = "";
		$this->website = "";
		$this->referred_by = '';
		$this->card_digit = '';
		$this->card_type = '';
		$this->subscription = '';
		$this->stripe_customer_id = '';
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
		$this->set_name($data['name']);
		$this->set_username($data['username']);
		$this->set_last_login($data['last_login']);
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
		$this->set_card_digit($data['card_digit']);
		$this->set_card_type($data['card_type']);
		$this->set_subscription($data['subscription']);
		$this->set_referred_by($data['referred_by']);
		$this->set_active($data['active']);
		$this->set_stripe_customer_id($data['stripe_customer_id']);
		
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

	public function get_name(){
		return $this->name;
	}
	public function get_username(){
		return $this->username;
	}
	public function get_last_login(){
		return $this->last_login;
	}

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
	
	public function get_referred_by(){
		return $this->referred_by;
	}
	
	public function get_card_digit(){
		return $this->card_digit;
	}
	
	public function get_card_type(){
		return $this->card_type;
	}
	
	public function get_subscription(){
		return $this->subscription;
	}
	
	public function get_active(){
		return $this->active;
	}

	public function get_stripe_customer_id(){
		return $this->stripe_customer_id;
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

	public function set_name($v){
		if($v){
			$this->name = $v;
		}
	}
	public function set_username($v){
		if($v){
			$this->username = $v;
		}
	}
	public function set_last_login($v){
		if($v){
			$this->last_login = $v;
		}
	}

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

	public function set_referred_by($v){
		$this->referred_by = $v;
	}
	
	public function set_card_digit($v){
		$this->card_digit = $v;
	}
	
	public function set_card_type($v){
		$this->card_type = $v;
	}
	
	public function set_subscription($v){
		$this->subscription = $v;
	}
	
	public function set_active($v){
		if($v){
			$this->active = self::ACTIVE;
		} else {
			$this->active = self::INACTIVE;
		}
	}

	public function set_stripe_customer_id($v){
		$this->stripe_customer_id = $v;
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
		$myarray['name'] = $this->name;
		$myarray['username'] = $this->username;
		$myarray['last_login'] = $this->last_login;
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
		$myarray['referred_by'] = $this->referred_by;
		$myarray['card_type'] = $this->card_type;
		$myarray['subscription'] = $this->subscription;
		$myarray['card_digit'] = $this->card_digit;
		$myarray['active'] = $this->active;
		$myarray['stripe_customer_id'] = $this->stripe_customer_id;
		return $myarray;
	}
	
	
	/*
	 * ------------------------------------------------------------
	 * FROM_ARRAY
	 * ------------------------------------------------------------
	 */

	public function from_array(&$data){
		$this->set_id($data['id']);
		$this->set_name($data['name']);
		$this->set_username($data['username']);
		$this->set_last_login($data['last_login']);
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
		$this->set_referred_by($data['referred_by']);
		$this->set_subscription($data['subscription']);
		$this->set_card_type($data['card_type']);
		$this->set_card_digit($data['card_digit']);
		$this->set_active($data['active']);
		$this->set_stripe_customer_id($data['stripe_customer_id']);
	}
	 
	/*
	 * ------------------------------------------------------------
	 * VALIDATE_NEW
	 * ------------------------------------------------------------
	 */
	
	public function validate_new(&$data){
		$error_flag = false;
		//name *req'd, unique
		if(empty($data['name'])){
			$error_flag = true;
			add_postback_error('name', 'Company name required');
		} elseif (db_exists(self::TABLE, 'name', $data['name'])){
			$error_flag = true;
			add_postback_error('name', $data['name'] . ' already exists in the database');
		} else {
			$this->set_name($data['name']);
		}
		
		//contact *req'd
		if(empty($data['contact'])){
			$error_flag = true;
			add_postback_error('contact', 'Primary Contact required');
		} else {
			$this->set_contact($data['contact']);
		}
		
// 		if(empty($data['phone_contact'])){
// 			$error_flag = true;
// 			add_postback_error('phone_contact', 'Contact phone required');
// 		}
		
// 		if(empty($data['phone_main'])){
// 			$error_flag = true;
// 			add_postback_error('phone_main', 'Main phone required');
// 		}
		
		if(isset($data['username']))
			$this->set_username($data['username']);
		if(isset($data['last_login']))
			$this->set_last_login ($data['last_login']);
                    
		//this stuff isn't as important
		$this->set_address1($data['address1']);
		$this->set_address2($data['address2']);
		$this->set_city($data['city']);
		$this->set_state($data['state']);
		$this->set_zip($data['zip']);
		$this->set_phone_main($data['phone_main']);
		$this->set_phone_contact($data['phone_contact']);
		$this->set_phone_fax($data['phone_fax']);
		$this->set_website($data['website']);
		$this->set_referred_by($data['referred_by']);
		$this->set_active(true);	
		
		if($error_flag){
			set_postback_msg("Invalid Data - Please review your entries below");
			return false;
		} else {
			return true;
		}
	}
	
	public function validate_edit(&$data){
		$error_flag = false;
		
		//name is skipped since it cannot be changed
		
		//contact *req'd
		if(empty($data['contact'])){
			$error_flag = true;
			add_postback_error('contact', 'Primary Contact required');
		} else {
			$this->set_contact($data['contact']);
		}
		
		if(empty($data['phone_contact'])){
			$error_flag = true;
			add_postback_error('phone_contact', 'Contact phone required');
		}
		
		if(empty($data['phone_main'])){
			$error_flag = true;
			add_postback_error('phone_main', 'Main phone required');
		}
                    
		if(isset($data['username']))
			$this->set_username($data['username']);
		if(isset($data['last_login']))
			$this->set_last_login ($data['last_login']);
		
		//this stuff isn't as important
		$this->set_address1($data['address1']);
		$this->set_address2($data['address2']);
		$this->set_city($data['city']);
		$this->set_state($data['state']);
		$this->set_zip($data['zip']);
		$this->set_phone_main($data['phone_main']);
		$this->set_phone_contact($data['phone_contact']);
		$this->set_phone_fax($data['phone_fax']);
		$this->set_website($data['website']);
		
		if($_SESSION['FT']['SEC_SUPPORT']){
			$activeCheck = isset($data['active']) ? $data['active'] : false;
			$this->set_active($activeCheck);
			$this->set_referred_by($data['referred_by']);
		}
		
		if($error_flag){
			set_postback_msg("Invalid Data - Please review your entries below");
			return false;
		} else {
			return true;
		}
	}
	
	public function validate_signup(&$data){
		$error_flag = false;

		if(empty($data['name'])){
			$error_flag = true;
			add_postback_error('name', 'Company name required');
		} elseif (db_exists(self::TABLE, 'name', $data['name'])){
			$error_flag = true;
			add_postback_error('name', $data['name'] . ' already exists in the database');
		} else {
			$this->set_name($data['name']);
		}
		
		if(empty($data['phone_main'])){
			$error_flag = true;
			add_postback_error('phone_main', 'Main phone required');
		} else {
			$this->set_phone_main($data['phone_main']);
		}
		
		//contact *req'd
		if(empty($data['contact'])){
			$error_flag = true;
			add_postback_error('contact', 'Primary Contact required');
		} else {
			$this->set_contact($data['contact']);
		}
		
		if(empty($data['phone_contact'])){
			$error_flag = true;
			add_postback_error('phone_contact', 'Contact phone required');
		} else {
			$this->set_phone_contact($data['phone_contact']);
		}
		
		if(isset($data['username']))
			$this->set_username($data['username']);
		if(isset($data['last_login']))
			$this->set_last_login ($data['last_login']);
                    
		//this stuff isn't as important
		$this->set_address1($data['address1']);
		$this->set_address2($data['address2']);
		$this->set_city($data['city']);
		$this->set_state($data['state']);
		$this->set_zip($data['zip']);
		$this->set_referred_by($data['referred_by']);
		$this->set_active(true);
		
		if($error_flag){
			set_postback_msg("Invalid Data - Please review your entries below");
			return false;
		} else {
			return true;
		}
		
	}
	 
	/*
	 * ------------------------------------------------------------
	 * INSERT_NEW
	 * ------------------------------------------------------------
	 */
	 

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
	
	public function update_card_info($company_id, $card_type, $card_digit){
		$where = array('id' => $company_id);
		$data = [
			"card_type" => $card_type,
			"card_digit" => $card_digit
		];
		// var_dump($data);
		$result = db_update(self::TABLE, $data, $where);
		
		return $result;
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
	 * Formbuilders - public
	 * ------------------------------------------------------------
	 */
	
	public function formbuilder_quick_insert(formbuilder &$form){
		$form->start_section('form_row');
			$form->add_subtitle('New Company');
		$form->end_section();
	
		$form->start_section('form_row');
			$form->add_custom('<a href="company.php?action=new">Create New</a>', 'Create new company');
		$form->end_section();
	}
	
	public function formbuilder_new(formbuilder &$form){
		
		$form->start_section('form_row');
			$form->add_subtitle('Create Company');
		$form->end_section();
		
		$form->start_section('form_row');
			$this->formbuilder_name($form, 1);
		$form->end_section();
		
		$this->formbuilder_company_address($form);
		
		$this->formbuilder_contact_info($form);
				
		$form->add_submit();
	 }
	 
	 public function formbuilder_signup(formbuilder &$form){
	 
	 	$form->start_section('form_row');
	 	$form->add_subtitle('Create Company - Step 1 of 3');
	 	$form->end_section();
	 
	 	$form->start_section('form_row');
	 	$this->formbuilder_name($form, 1);
	 	$form->end_section();
	 
	 	$this->formbuilder_company_address($form);
	 
	 	$this->formbuilder_contact_info($form);
	 	
	 	$this->formbuilder_referral($form);
	 
	 	$form->add_submit();
	 }
	 
	 public function formbuilder_edit(formbuilder &$form){
		
		$form->start_section('form_row');
			$form->add_subtitle('Edit Company');
		$form->end_section();
		
		$form->start_section('form_row');
			$this->formbuilder_name($form, 0);
		$form->end_section();

		$this->formbuilder_company_address($form);
		
		$this->formbuilder_contact_info($form);
		
		
		
		if($_SESSION['FT']['SEC_SUPPORT']){
			
			$this->formbuilder_referral($form);
			
			$form->start_section('form_row');
			$this->formbuilder_active($form);
			$form->end_section();
		} else {
			$form->add_hidden('active', $this->get_active());
		}
				
		$form->add_submit();
				
	 }

	/*
	 * ------------------------------------------------------------
	 * Formbuilders - private
	 * ------------------------------------------------------------
	 */
	 
	 private function formbuilder_company_address(formbuilder &$form){
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
	 
	 private function formbuilder_contact_info(formbuilder &$form){
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
	 }
	 
	 private function formbuilder_referral(formbuilder &$form){
	 	$form->start_section('form_row');
	 	$form->add_subtitle('Referral');
	 	$form->end_section();
	 	$form->start_section('form_row');
	 	$this->formbuilder_referred_by($form);
	 	$form->end_section();
	 }
	 
	 private function formbuilder_id_hidden(&$form){
	 	$form->add_hidden('id', $this->get_id());
	 }
	 
	 private function formbuilder_name(&$form, $editable = 1){
	 	$form->add_text('name', 'Company Name', $this->get_name(), 1, $editable);
	 }
	 
	 private function formbuilder_contact(&$form, $editable = 1){
	 	$form->add_text('contact', 'Main Contact', $this->get_contact(), 1, $editable);
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
	 	$form->add_text('phone_main', 'Main Phone', $this->get_phone_main(), 1, $editable);
	 }
	 
	 private function formbuilder_phone_contact(&$form, $editable = 1){
	 	$form->add_text('phone_contact', 'Contact Phone', $this->get_phone_contact(), 1, $editable);
	 }
	 
	 private function formbuilder_phone_fax(&$form, $editable = 1){
	 	$form->add_text('phone_fax', 'Fax Number', $this->get_phone_fax(), 0, $editable);
	 }
	 
	 private function formbuilder_website(&$form, $editable = 1){
	 	$form->add_text('website', 'Website', $this->get_website(), 0, $editable);
	 }
	 
	 private function formbuilder_referred_by(&$form, $editable = 1){
	 	$form->add_text('referred_by', 'Referred by', $this->get_referred_by(), 0, $editable);
	 }
	 
	 private function formbuilder_active(formbuilder &$form){
	 	$form->add_checkbox('active', 'active', 'Active', $this->get_active());
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
	 
	public static function get_companies(){
		$columns = array('id', 'name val');
		$order_by = "ORDER BY id asc";
		
		$data = db_select(self::TABLE, 1, '', $columns, '', $order_by);
		
		return $data;
	}
	
	public static function get_active_companies(){
		$columns = array('id', 'name val');
		$order_by = 'ORDER BY LOWER(name) asc';
		$where = array('active'=>self::ACTIVE);
		$data = db_select(self::TABLE, $where, '', $columns, '', $order_by);
		
		return $data;
	}
	
	//putting this here because Support can use it to pre-empt any other companyid pulls
	//this way we can do things like re-assign deals, users and impersonate  
	public static function formbuilder_support_company_id(formbuilder &$form, $default = 0, $editable = 1){
		$form->add_select('company_id', company::get_companies(), 'Company ID', $default, $editable);
	}
	
	public static function company_id_is_active($company_id){
		$id = intval($company_id);
		if(empty($id)){
			return false;
		}
		$where = array('id'=>$id, 'active'=>1);
		return db_exists_multi(self::TABLE, $where);
	}
	
	public static function company_name($id){
		$id = intval($id);
		if($id<=0){return 'Empty';}
		$where = array('id'=>$id);
		$columns = 'name';
		$result = db_select_one(self::TABLE, $where, '', $columns);
		
		if(empty($result)){ return 'Empty';}
		return $result['name'];
	}
	
	/*
	 * ------------------------------------------------------------
	 * PRIVATE STATIC FUNCTIONS
	 * ------------------------------------------------------------
	 */
	 	
	
	
	
}