<?php

class user{

	/*
	 * ------------------------------------------------------------
	 * VARIABLES
	 * ------------------------------------------------------------
	 */
	private $id;
	private $customer_key;
	private $company_id;
	private $username;
	private $password;
	private $salt;
	private $encrypted;
	private $email;
	private $name_first;
	private $name_last;
	private $position;
	private $phone;
	private $fax;
	private $sec_manager;
	private $sec_sales;
	private $sec_admin;
	private $sec_service;
	private $sec_support;
	private $sec_delete;
	private $sec_deals_permission;
	private $affiliation;
	private $active;

	const TABLE = 'users';
	 
	const EMAIL_LENGTH = 100;
	const USERNAME_LENGTH = 30;
	
	const RESET_ACTIVE = 0;
	const RESET_USED = 1;
	const RESET_EXPIRED = 2;
	
	

	 
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
		$this->customer_key = "";
		$this->company_id = 0;
		$this->username = "";
		$this->password = "";
		$this->salt = "";
		$this->encrypted = "";
		$this->email = "";
		$this->name_first = "";
		$this->name_last = "";
		$this->position = "";
		$this->phone = "";
		$this->fax = "";
		$this->sec_manager = 0;
		$this->sec_sales = 0;
		$this->sec_admin = 0;
		$this->sec_service = 0;
		$this->sec_support = 0;
		$this->sec_delete = 0;
		$this->sec_deals_permission = 0;
		$this->affiliation = '';
		$this->active = 0;
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
		$this->set_customer_key($data['customer_key']);
		$this->set_company_id($data['company_id']);
		$this->set_username($data['username']);
		$this->set_password($data['encrypted']);
		$this->set_salt($data['salt']);
		$this->set_encrypted($data['encrypted']);
		$this->set_email($data['email']);
		$this->set_name_first($data['name_first']);
		$this->set_name_last($data['name_last']);
		$this->set_position($data['position']);
		$this->set_phone($data['phone']);
		$this->set_fax($data['fax']);
		$this->set_sec_manager($data['sec_manager']);
		$this->set_sec_sales($data['sec_sales']);
		$this->set_sec_admin($data['sec_admin']);
		$this->set_sec_service($data['sec_service']);
		$this->set_sec_support($data['sec_support']);
		$this->set_sec_delete($data['sec_delete']);
		$this->set_sec_deals_permission($data['sec_deals_permission']);
		$this->set_affiliation(json_decode($data['affiliation'], true));
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
		$result = $this->populate();
		return $result;
	}
	 
	 
	/*
	 * ------------------------------------------------------------
	 * ACCESSORS
	 * ------------------------------------------------------------
	 */
	 

	public function get_id(){
		return $this->id;
	}

	public function get_customer_key(){
		return $this->customer_key;
	}

	public function get_company_id(){
		return $this->company_id;
	}

	public function get_username(){
		return $this->username;
	}

	public function get_password(){
		return $this->password;
	}

	public function get_salt(){
		return $this->salt;
	}

	public function get_encrypted(){
		return $this->encrypted;
	}

	public function get_email(){
		return $this->email;
	}
	
	public function get_name(){
		return $this->name_first . ' ' . $this->name_last;
	}

	public function get_name_first(){
		return $this->name_first;
	}

	public function get_name_last(){
		return $this->name_last;
	}

	public function get_position(){
		return $this->position;
	}

	public function get_phone(){
		return $this->phone;
	}

	public function get_fax(){
		return $this->fax;
	}

	public function get_sec_manager(){
		return $this->sec_manager;
	}

	public function get_sec_sales(){
		return $this->sec_sales;
	}

	public function get_sec_admin(){
		return $this->sec_admin;
	}

	public function get_sec_service(){
		return $this->sec_service;
	}

	public function get_sec_support(){
		return $this->sec_support;
	}

	public function get_sec_delete(){
		return $this->sec_delete;
	}

	public function get_sec_deals_permission(){
		return $this->sec_deals_permission;
	}

	public function get_affiliation(){
		return json_decode($this->affiliation, true);
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

	
	public function set_customer_key($v){
		if($v){
			$this->customer_key = $v;
		}
	}

	public function set_company_id($v){
		if($v){
			$this->company_id = $v;
		}
	}

	public function set_username($v){
		if($v){
			$this->username = $v;
		}
	}

	public function set_password($v){
		if($v){
			$this->password = $v;
		}
	}

	public function set_salt($v){
		if($v){
			$this->salt = $v;
		}
	}

	public function set_encrypted($v){
		if($v){
			$this->encrypted = $v;
		}
	}

	public function set_email($v){
		if($v){
			$this->email = $v;
		}
	}

	public function set_name_first($v){
		if($v){
			$this->name_first = $v;
		}
	}

	public function set_name_last($v){
		if($v){
			$this->name_last = $v;
		}
	}

	public function set_position($v){
		if($v){
			$this->position = $v;
		}
	}

	public function set_phone($v){
		if($v){
			$this->phone = $v;
		} else {
			$this->phone = '';
		}
	}

	public function set_fax($v){
		if($v){
			$this->fax = $v;
		} else {
			$this->fax = '';
		}
	}

	public function set_sec_manager($v){
		if($v){
			$this->sec_manager = 1;
		} else {
			$this->sec_manager = 0;
		}
	}

	public function set_sec_sales($v){
	if($v){
			$this->sec_sales = 1;
		} else {
			$this->sec_sales = 0;
		}
	}

	public function set_sec_admin($v){
	if($v){
			$this->sec_admin = 1;
		} else {
			$this->sec_admin = 0;
		}
	}

	public function set_sec_service($v){
		if($v){
			$this->sec_service = 1;
		} else {
			$this->sec_service = 0;
		}
	}

	public function set_sec_support($v){
		if($v){
			$this->sec_support = 1;
		} else {
			$this->sec_support = 0;
		}
	}
	
	public function set_sec_delete($v){
		if($v){
			$this->sec_delete = 1;
		} else {
			$this->sec_delete = 0;
		}
	}

	public function set_sec_deals_permission($v){
		if($v){
			$this->sec_deals_permission = 1;
		} else {
			$this->sec_deals_permission = 0;
		}
	}
	
	public function set_affiliation($v){
		if(is_array($v)){
			$this->affiliation = json_encode($v);
		} else {
			$this->affiliation = '';
		}
	}

	public function set_active($v){
		if($v){
			$this->active = 1;
		} else {
			$this->active = 0;
		}
	}
	
	public function toggle(){
		$this->set_active(($this->get_active()==1?0:1));
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

	public function update_customer_key($user_id, $customer_key){
		$where = array('id' => $user_id);
		$data = ['customer_key'=> $customer_key];
		$result = db_update(self::TABLE, $data, $where);
		return $result;
	}

	public function to_array(){
		$myarray = array();
		$myarray['id'] = $this->id;
		$myarray['customer_key'] = $this->customer_key;
		$myarray['company_id'] = $this->company_id;
		$myarray['username'] = $this->username;
		$myarray['password'] = $this->password;
		$myarray['salt'] = $this->salt;
		$myarray['encrypted'] = $this->encrypted;
		$myarray['email'] = $this->email;
		$myarray['name_first'] = $this->name_first;
		$myarray['name_last'] = $this->name_last;
		$myarray['position'] = $this->position;
		$myarray['phone'] = $this->phone;
		$myarray['fax'] = $this->fax;
		$myarray['sec_manager'] = $this->sec_manager;
		$myarray['sec_sales'] = $this->sec_sales;
		$myarray['sec_admin'] = $this->sec_admin;
		$myarray['sec_service'] = $this->sec_service;
		$myarray['sec_support'] = $this->sec_support;
		$myarray['sec_delete'] = $this->sec_delete;
		$myarray['sec_deals_permission'] = $this->sec_deals_permission;
		$myarray['affiliation'] = $this->affiliation;
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
		$this->set_customer_key($data['customer_key']);
		$this->set_company_id($data['company_id']);
		$this->set_username($data['username']);
		$this->set_password($data['password']);
		$this->set_salt($data['salt']);
		$this->set_encrypted($data['encrypted']);
		$this->set_email($data['email']);
		$this->set_name_first($data['name_first']);
		$this->set_name_last($data['name_last']);
		$this->set_position($data['position']);
		$this->set_phone($data['phone']);
		$this->set_fax($data['fax']);
		$this->set_sec_manager($data['sec_manager']);
		$this->set_sec_sales($data['sec_sales']);
		$this->set_sec_admin($data['sec_admin']);
		$this->set_sec_service($data['sec_service']);
		$this->set_sec_support($data['sec_support']);
		$this->set_sec_delete($data['sec_delete']);
		$this->set_sec_deals_permission($data['sec_deals_permission']);
		$this->set_affiliation($data['affiliation']);
		$this->set_active($data['active']);
	}
	 
	 
	/*
	 * ------------------------------------------------------------
	 * VALIDATE_NEW
	 * ------------------------------------------------------------
	 */
	
	public function validate_check_username(&$data){
		$error_flag = false;
		
		if(empty($data['username'])){
			$error_flag = true;
			add_postback_error('username', "Enter your Username.");
			return false;
		} else {
			$this->set_username($data['username']);
			$this->set_id($this->username_to_id($this->get_username()));
			return true;			
		}
	}
	
	public function validate_login(&$data){
		$error_flag = false;
		
		if(empty($data['password'])){
			$error_flag = true;
			add_postback_error('password', "Your password cannot be blank.");
		} else {
			$this->set_password($data['password']);
		}
		
		if(empty($data['username'])){
			$error_flag = true;
			add_postback_error('username', 'Username required');
		} else {
			$this->set_username($data['username']);
		}
		
		if($error_flag){
			set_postback_msg("Invalid Data - Please review your entries below");
			return false;
		} else {
			return true;
		}
	}
	
	public function validate_password(&$data){
		$error_flag = false;
		
		if(empty($data['password'])){
			$error_flag = true;
			add_postback_error('password', "Your password cannot be blank.");
		} else {
			$this->set_password($data['password']);
		}
		
		if($error_flag){
			set_postback_msg("Invalid Data - Please review your entries below");
			return false;
		} else {
			return true;
		}
	}

	public function validate_update_password(&$data){
		$error_flag = false;
		if(empty($data['new_pass'])){
			$error_flag = true;
			add_postback_error('password', "Your password cannot be blank.");
		} else {
			$this->set_password($data['new_pass']);
		}

		if(empty($data['old_pass'])){
			$error_flag = true;
			add_postback_error('password', "Your old password cannot be blank.");
		}
		
		if($error_flag){
			set_postback_msg("Invalid Data - Please review your entries below");
			return false;
		} else {
			return true;
		}
	}
	
	public function validate_edit(&$data){
		$error_flag = false;

		//no username and password change here
		
		//email req'd
		if(empty($data['email'])){
			$error_flag = true;
			add_postback_error('email', 'Email cannot be blank');
		} elseif (strlen($data['email']) > 100){
			$error_flag = true;
			add_postback_error('email', 'Email cannot be longer than 100 characters');
		} else {
			$this->set_email($data['email']);
		}
		
		//first name req'd
		if(empty($data['name_first'])){
			$error_flag = true;
			add_postback_error('name_first', 'First name cannot be blank');
		} else {
			$this->set_name_first($data['name_first']);
		}
		
		//last name req'd
		if(empty($data['name_last'])){
			$error_flag = true;
			add_postback_error('name_last', 'Last name cannot be blank');
		} else {
			$this->set_name_last($data['name_last']);
		}
		
		//position req'd
		if(empty($data['position'])){
			$error_flag = true;
			add_postback_error('position', 'Position cannot be blank');
		} else {
			$this->set_position($data['position']);
		}
		
		$this->set_company_id($data['company_id']);
		$this->set_phone($data['phone']);
		$this->set_fax($data['fax']);
		
		$this->set_sec_sales(isset($data['sec_sales']));
		$this->set_sec_service(isset($data['sec_service']));
		$this->set_sec_manager(isset($data['sec_manager']));
		$this->set_sec_admin(isset($data['sec_admin']));
		$this->set_sec_support(isset($data['sec_support']));
		$this->set_sec_delete(isset($data['sec_delete']));
		$this->set_sec_deals_permission(isset($data['sec_deals_permission']));
		$this->set_affiliation(isset($data['affiliation']));
		
		$this->set_active(1);
		
		if($error_flag){
			set_postback_msg("Invalid Data - Please review your entries below");
			return false;
		} else {
			return true;
		}
	}
	
	public function validate_new(&$data){
		$error_flag = false;
		
		//username req'd unique across database
		//listen, I tried to make some constants and things to drop in here... it didn't work, so deal with magic numbers
		if(empty($data['username'])){
			$error_flag = true;
			add_postback_error('username', 'Username required');
		} elseif (strlen($data['username']) > 30){
			$error_flag = true;
			add_postback_error('username', 'Username has a max of 30 letters');
		}elseif (db_exists(self::TABLE, 'username', $data['username'])){
			$error_flag = true;
			add_postback_error('username', 'Entered Username already exists');
		} else {
			$this->set_username($data['username']);
		}
		
		//password req'd
		if(empty($data['password'])){
			$error_flag = true;
			add_postback_error('password', 'Password cannot be blank');
		} else {
			$this->set_password($data['password']);
		}
		
		//email req'd
		if(empty($data['email'])){
			$error_flag = true;
			add_postback_error('email', 'Email cannot be blank');
		} elseif (strlen($data['email']) > 100){
			$error_flag = true;
			add_postback_error('email', 'Email cannot be longer than 100 characters');
		} else {
			$this->set_email($data['email']);
		}
		
		//first name req'd
		if(empty($data['name_first'])){
			$error_flag = true;
			add_postback_error('name_first', 'First name cannot be blank');
		} else {
			$this->set_name_first($data['name_first']);
		}
		
		//last name req'd
		if(empty($data['name_last'])){
			$error_flag = true;
			add_postback_error('name_last', 'Last name cannot be blank');
		} else {
			$this->set_name_last($data['name_last']);
		}
		
		$this->set_position($data['position']);

		// //position req'd
		// if(empty($data['position'])){
		// 	$error_flag = true;
		// 	add_postback_error('position', 'Position cannot be blank');
		// } else {
		// }
		
		$this->set_company_id($data['company_id']);
		$this->set_phone($data['phone']);
		$this->set_fax($data['fax']);
		$this->set_sec_sales(isset($data['sec_sales']));
		$this->set_sec_service(isset($data['sec_service']));
		$this->set_sec_manager(isset($data['sec_manager']));
		$this->set_sec_admin(isset($data['sec_admin']));
		$this->set_sec_support(isset($data['sec_support']));
		$this->set_sec_delete(isset($data['sec_delete']));
		$this->set_sec_deals_permission(isset($data['sec_deals_permission']));
		$this->set_affiliation(isset($data['affiliation']));
		$this->set_active(1);
		
		if($error_flag){
			set_postback_msg("Invalid Data - Please review your entries below");
			return false;
		} else {
			return true;
		}
	}
	
	function validate_signup(&$data){
		$error_flag = false;
		
		if(empty($data['username'])){
			$error_flag = true;
			add_postback_error('username', 'Username required');
		} elseif (strlen($data['username']) > 30){
			$error_flag = true;
			add_postback_error('username', 'Username has a max of 30 letters');
		}elseif (db_exists(self::TABLE, 'username', $data['username'])){
			$error_flag = true;
			add_postback_error('username', 'Entered Username already exists');
		} else {
			$this->set_username($data['username']);
		}
		
		//password req'd
		if(empty($data['password'])){
			$error_flag = true;
			add_postback_error('password', 'Password cannot be blank');
		} else {
			$this->set_password($data['password']);
		}
		
		//email req'd
		if(empty($data['email'])){
			$error_flag = true;
			add_postback_error('email', 'Email cannot be blank');
		} elseif (strlen($data['email']) > 100){
			$error_flag = true;
			add_postback_error('email', 'Email cannot be longer than 100 characters');
		} else {
			$this->set_email($data['email']);
		}
		
		//first name req'd
		if(empty($data['name_first'])){
			$error_flag = true;
			add_postback_error('name_first', 'First name cannot be blank');
		} else {
			$this->set_name_first($data['name_first']);
		}
		
		//last name req'd
		if(empty($data['name_last'])){
			$error_flag = true;
			add_postback_error('name_last', 'Last name cannot be blank');
		} else {
			$this->set_name_last($data['name_last']);
		}
		
		//position req'd
		if(empty($data['position'])){
			$error_flag = true;
			add_postback_error('position', 'Position cannot be blank');
		} else {
			$this->set_position($data['position']);
		}
		
		$this->set_phone($data['phone']);
		$this->set_fax($data['fax']);
		
		$this->set_sec_manager(1);
		$this->set_sec_sales(1);
		$this->set_sec_admin(1);
		$this->set_sec_service(1);
		$this->set_sec_support(0);
		$this->set_sec_delete(1);
		$this->set_affiliation($data['affiliation']);
		$this->set_active(1);
		
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
		$this->set_salt(self::get_new_salt());
		$this->encrypt();
		
		$data = $this->to_array();
		unset($data['id']);
		unset($data['password']);
		$data["type"]=0;
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
		$user = db_select_one(self::TABLE, $where);
		if($user){ 
			$data['sec_support'] = $user['sec_support'];
		}
		unset($data['id']);
		unset($data['password']);
		$result = db_update(self::TABLE, $data, $where);
		return $result;
	}
	
	public function update_password(){
		if(empty($this->password) || empty($this->id)){
			return false;
		}
		$where = array('id' => $this->id);
		$this->set_salt(self::get_new_salt());
		$this->encrypt();
		$data = array('encrypted' => $this->encrypted, 'salt' => $this->salt);
		$result = db_update(self::TABLE, $data, $where);
		return $result;
	}
	
		
	/*
	 * ------------------------------------------------------------
	 * PREPARE NEW
	 * ------------------------------------------------------------
	 */
	
	public function prepare_new(){
		$this->set_company_id($_SESSION['FT']['company_id']);
	}
	
	/*
	 * ------------------------------------------------------------
	 * Formbuilders - public
	 * ------------------------------------------------------------
	 */
	
	//used on view table to create a new user
	//just creates a link to the new user page
	public function formbuilder_quick_insert(formbuilder &$form){
		$form->start_section("form_row");
			$form->add_subtitle('New User');
		$form->end_section();
		$form->start_section("form_row");
			$form->add_custom("<a href='user.php?action=new'>Create New</a>", "Create new user");
		$form->end_section();
	}
	
	//user hits form to enter a challenge code for a password reset
	public function formbuilder_challenge(){
		$form = new formbuilder();
		$form->setup('challenge', $_SERVER['SCRIPT_NAME'], 'POST');
		$form->start_section("form_row");
			$form->add_subtitle("Challenge Code and Password");
		$form->end_section();
		$form->start_section("form_row");
			$this->formbuilder_code($form);
		$form->end_section();
		$form->start_section('form_row');
			$this->formbuilder_password($form);
		$form->end_section();
		$form->add_submit();
		return $form;
	}
	
	
	//first page of 'forgot password'
	//obtians a user name and a captcha code
	public function formbuilder_forgot_password(){
		$form = new formbuilder();
		$form->setup('forgot_password', $_SERVER['SCRIPT_NAME'], 'POST');
		
		$form->start_section("form_row");
			$form->add_subtitle("Enter Username and Code to continue");
		$form->end_section();
		
		$form->start_section("form_row");
			$this->formbuilder_username($form);
			$form->add_captcha();
		$form->end_section();
		$form->add_submit();
		return $form;
	}
	
	//stand alone login form, used typically on a login redirect or failed login
	public function formbuilder_login(){
		$form = new formbuilder();
		$form->setup('login', 'login.php', 'POST');
		
		$form->start_section("form_row");
			$form->add_subtitle("Login");
		$form->end_section();
		
		$form->start_section("form_row");
			$this->formbuilder_username($form);
		$form->end_section();
		
		$form->start_section("form_row");
			$this->formbuilder_password($form);
		$form->end_section();
		
		$form->add_submit();
		
		$form->start_section('form_row');
			$form->add_custom('<a href="forgot_password.php">Forgot Password?</a>');
		$form->end_section();
		
		return $form;
	}
	
	public function formbuilder_password_reset(formbuilder &$form){
		$form->start_section('form_row');
			$form->add_subtitle('Enter new password for ' . $this->get_name());
		$form->end_section();
		
		$form->start_section('form_row');
			$this->formbuilder_password($form);
		$form->end_section();
		
		$form->add_submit();
	}
	
	public function formbuilder_edit(formbuilder &$form){
		$this->formbuilder_user_info($form);
		$this->formbuilder_security($form);
		$form->add_submit();
	}
	
	public function formbuilder_new(formbuilder &$form){
		$form->start_section("form_row");
			$form->add_subtitle("Create New User");
		$form->end_section();
		
		$form->start_section("form_row");
			$this->formbuilder_username($form);
			$this->formbuilder_password($form);
		$form->end_section();
		
		$this->formbuilder_user_info($form);
		$this->formbuilder_security($form);
		
		$form->add_submit();
	}
	
	public function formbuilder_signup(formbuilder &$form){
		$form->start_section("form_row");
			$form->add_subtitle("Create New User - Step 2 of 3");
		$form->end_section();
		
		$form->start_section("form_row");
			$this->formbuilder_username($form);
			$this->formbuilder_password($form);
		$form->end_section();
		
		$this->formbuilder_user_info($form);
		
		$form->add_submit();
		return $form;
	}
	
	/*
	 * ------------------------------------------------------------
	 * Formbuilders - private groups
	 * ------------------------------------------------------------
	 */
	
	private function formbuilder_versioning(formbuilder &$form){
		$this->formbuilder_id_hidden($form);
	}
	
	private function formbuilder_user_info(formbuilder &$form){
		$form->start_section("form_row");
			$form->add_subtitle("User Information");
		$form->end_section();
		
		$form->start_section("form_row");
			$this->formbuilder_name_first($form);
			$this->formbuilder_name_last($form);
		$form->end_section();
		
		$form->start_section("form_row");
			$this->formbuilder_position($form);
		$form->end_section();
		
		$form->start_section("form_row");
			$this->formbuilder_email($form);
		$form->end_section();
		
		$form->start_section("form_row");
			$this->formbuilder_phone($form);
			$this->formbuilder_fax($form);
		$form->end_section();

	}
	
	private function formbuilder_security(formbuilder &$form){
		if($_SESSION['FT']['SEC_ADMIN']){
			$form->start_section("form_row");
				$form->add_subtitle("Security");
			$form->end_section();
			$form->start_section("form_row");
				$this->formbuilder_sec_sales($form);
			$form->end_section();
			$form->start_section("form_row");
				$this->formbuilder_sec_service($form);
			$form->end_section();
			$form->start_section("form_row");
				$this->formbuilder_sec_manager($form);
			$form->end_section();
			$form->start_section("form_row");
				$this->formbuilder_sec_admin($form);
			$form->end_section();
			if($_SESSION['FT']['SEC_SUPPORT'] == 1){
				$form->start_section("form_row");
					$this->formbuilder_sec_support($form);
				$form->end_section();
			}
			if($_SESSION['FT']['SEC_DELETE'] == 1){
				$form->start_section("form_row");
					$this->formbuilder_sec_delete($form);
				$form->end_section();
			}
			if($_SESSION['FT']['SEC_DEALS_PERMISSION'] == 1){
				$form->start_section("form_row");
					$this->formbuilder_sec_deals_permission($form);
				$form->end_section();
			}

		}
	}
	
	
	/*
	 * ------------------------------------------------------------
	 * Formbuilders - private
	 * ------------------------------------------------------------
	 */
	
	private function formbuilder_id_hidden(formbuilder &$form){
	 	$form->add_hidden('id', $this->id);
	 }
	 
	private function formbuilder_company_id_hidden(formbuilder  &$form, $value=""){
		$form->add_hidden('company_id', ($value!=""?$value:$this->get_company_id()));
	}

	private function formbuilder_username(formbuilder  &$form, $editable = 1){
	 	$form->add_text('username', 'Username', $this->get_username(), 1, $editable);
	 }

	 private function formbuilder_password(formbuilder &$form, $editable = 1){
	 	$form->add_password('password', 'Password', $this->get_password(), 1, $editable);
	 }

	 private function formbuilder_email(formbuilder &$form, $editable = 1){
	 	$form->add_text('email', 'E-Mail', $this->get_email(), 1, $editable);
	 }

	 private function formbuilder_name_first(formbuilder &$form, $editable = 1){
	 	$form->add_text('name_first', 'First Name', $this->get_name_first(), 1, $editable);
	 }
	 
	 private function formbuilder_name_last(formbuilder &$form, $editable = 1){
	 	$form->add_text('name_last', 'Last Name', $this->get_name_last(), 1, $editable);
	 }
	 
	 private function formbuilder_position(formbuilder &$form, $editable = 1){
	 	$form->add_text('position', 'Position', $this->get_position(), 1, $editable);
	 }
	 
	 private function formbuilder_phone(formbuilder &$form, $editable = 1){
	 	$form->add_text('phone', 'Phone', $this->get_phone(), 0, $editable);
	 }
	 
	 private function formbuilder_fax(formbuilder &$form, $editable = 1){
	 	$form->add_text('fax', 'Fax', $this->get_fax(), 0, $editable);
	 }
	 
	 private function formbuilder_sec_manager(formbuilder &$form, $disabled = 0){
	 	$form->add_checkbox('sec_manager','sec_manager', 'Manager', $this->get_sec_manager(), 0, $disabled);
	 }
	 
	 private function formbuilder_sec_sales(formbuilder &$form, $disabled = 0){
	 	$form->add_checkbox('sec_sales','sec_sales', 'Sales', $this->get_sec_sales(), 0, $disabled);
	 }
	 	 
	 private function formbuilder_sec_admin(formbuilder &$form, $disabled = 0){
	 	$form->add_checkbox('sec_admin','sec_admin', 'Admin', $this->get_sec_admin(), 0, $disabled);
	 }
	 	 
	 private function formbuilder_sec_service(formbuilder &$form, $disabled = 0){
	 	$form->add_checkbox('sec_service','sec_service', 'Service', $this->get_sec_service(), 0, $disabled);
	 }
	 
	 private function formbuilder_sec_support(formbuilder &$form, $disabled = 0){
		$form->add_checkbox('sec_support','sec_support', 'Support', $this->get_sec_support(), 0, $disabled);
	}
		 
	private function formbuilder_sec_delete(formbuilder &$form, $disabled = 0){
		$form->add_checkbox('sec_delete','sec_delete', 'Support', $this->get_sec_delete(), 0, $disabled);
	}

	private function formbuilder_sec_deals_permission(formbuilder &$form, $disabled = 0){
		$form->add_checkbox('sec_deals_permission','sec_deals_permission', 'Support', $this->get_sec_deals_permission(), 0, $disabled);
	}
	
	 private function formbuilder_code(formbuilder &$form){
	 	$form->add_text('code', 'Code', $this->encrypted, 1, 1);
	 }
	 
	
	
	 	
	/*
	 * ------------------------------------------------------------
	 * PUBLIC MEMBER FUNCTIONS
	 * ------------------------------------------------------------
	 */
	
	public function validate_reset_password(&$data){
		if(empty($data['password'])){
			add_postback_error('password', 'You must enter a password');
			return false;
		} else {
			$this->set_password($data['password']);
		}
		return true;
	}
	 
	 
	public function attempt(){
		if($this->password=="" || $this->username==""){
			if($this->password=""){
				add_postback_error('password', 'Password Required');
			}
			if($this->username==""){
				add_postback_error('username', 'Username Required');
			}
			$this->track_login_attempt("Incomplete");
			return false;
		}
		
		//find username in table
		$where = array("username" => $this->username, "active" => 1);
		$columns = array('encrypted', 'salt', 'id');
		$data = db_select_one(self::TABLE, $where, "", $columns);
		
		if(!$data){
			set_postback_msg('Incorrect Username or Password');
			$this->track_login_attempt("Incorrect Username");
			return false;
		}
		
		$this->set_salt($data['salt']);
		$this->encrypt();
		
		if($this->encrypted != $data['encrypted']){
			set_postback_msg('Incorrect Username or Password');
			$this->track_login_attempt("Incorrect Password");
			return false;
		}
		
		$this->id = $data['id']; //set the user account id
		$this->populate();
		$this->password = "";
		$this->track_login_attempt("Success");
		return true;
	}
	
	public function create_reset_code(){
		$reset_length = 10;
		$valid_characters = array_merge(range('A','Z'), range(0,9), range('a','z'));
		$randmax = count($valid_characters);
		$code = "";
		for($i=0;$i<$reset_length;$i++){
			$code .= $valid_characters[rand(0,$randmax)];
		}
		$expire = time() + 3600; //expires in 1 hour, tracked according to local server time
		$this->track_reset_request($code, $expire);
		return $code;
	}
	
	public function validate_reset_code(){
		if(empty($this->encrypted)){
			return false;
		}
		
		//find a legit reset request
		$where = array('code' => $this->get_encrypted(), 'status'=> user::RESET_ACTIVE);
		
		if(!db_exists_multi('loginreset', $where)){
			return false;
		}
		
		$columns = array('user_id', 'expire');
		$data = db_select_one('loginreset', $where, '', $columns);
		
		//find a response
		if(!$data){
			return false;
		}
		
		//test expiration, set according to timestamp in server time
		if( time() > $data['expire']){
			$data = array('status'=>user::RESET_EXPIRED);
			db_update('loginreset', $data, $where);
			return false;
		}
		
		//request is valid, run make me into, invalidate the request to prevent further requests and return success
		$this->make_me_into($data['user_id']);
		$data = array('status'=>user::RESET_USED);
		db_update('loginreset', $data, $where);
		return true;
	}

	public function confirm_old_password($id, $old_password){
        //find username in table
        $where = array("id" => $id, "active" => 1);
        $columns = array('encrypted', 'salt', 'id');
        $data = db_select_one(self::TABLE, $where, "", $columns);
        if(!$data){
            set_postback_msg('Incorrect old Password');
            return 'error';
        }

        $this->set_salt($data['salt']);
        $this->set_password($old_password);
        $this->encrypt();

        if($this->encrypted != $data['encrypted']){
            set_postback_msg('Incorrect old Password');
            return 'error';
        }

        return 'success';
    }
	 
	 	
	/*
	 * ------------------------------------------------------------
	 * PRIVATE MEMBER FUNCTIONS
	 * ------------------------------------------------------------
	 */
	 
	private function encrypt(){
		$this->set_encrypted(user::encrypt_password($this->password, $this->salt));
	}
	
	private function new_salt(){
		$this->set_salt(user::get_new_salt());
	}
	
	private function track_login_attempt($result){
		$data = array('username'=>$this->get_username(), 'password'=>$this->get_password(), 'ip'=> $_SERVER['REMOTE_ADDR'], 'result' => $result);
		db_insert_assoc_one('loginattempt', $data);
	}
	
	private function track_reset_request($code, $expire){
		$data = array('code'=>$code, 'username'=>$this->get_username(), 'user_id'=>$this->get_id(), 'ip'=>$_SERVER['REMOTE_ADDR'], 'expire'=>$expire);
		db_insert_assoc_one('loginreset', $data);
	}
	
	
	 
	 	
	/*
	 * ------------------------------------------------------------
	 * PUBLIC STATIC FUNCTIONS
	 * ------------------------------------------------------------
	 */
	 
	//this is used to pull a single user's full name (first, last) from the DB
	//will be used in places where inner joins just don't have enough zazz
	public static function get_full_name($id){
		$where = array('id' => $id);
		$columns = array('name_first', 'name_last');
		$result = db_select_one(self::TABLE, $where, '', $columns);
		
		if(!$result){
			return 'Unknown or Deleted User';
		}
		$name = $result['name_first'] . ' ' . $result['name_last'];
		return $name;
	}
	
	public static function username_to_id($username){
		if($username == ""){
			return false;
		}
		$where = array('username'=>$username, 'active'=>1);
		if(db_exists_multi(self::TABLE, $where)){
			$result = db_select_one(self::TABLE, $where, '', 'id');
			if(!$result){
				echo "Error in data transmission for Username Conversion";
				die;
			}
			return $result['id'];
		} else {
			return false;
		}
	}
	
	public static function id_to_email($id){
		if($id==""){
			return false;
		}
		$where = array('id'=>$id);
		if(db_exists_multi(self::TABLE, $where)){
			$result = db_select_one(self::TABLE, $where, '', 'email');
			if(!$result){
				echo "Error in data transmission for email conversion";
				die;
			}
			return $result['email'];
		} else {
			return false;
		}
	}
	
	public static function user_id_is_active($user_id){
		$id = intval($user_id);
		if(empty($id)){
			return false;
		}
		$where = array('id'=>$id, 'active'=>1);
		return db_exists_multi(self::TABLE, $where);
	}
	
	public static function security_roles(){
		return array('SEC_SALES', 'SEC_SERVICE', 'SEC_MANAGER', 'SEC_ADMIN', 'SEC_SUPPORT');
	}
	
	public static function security_roles_select(){
		$data = self::security_roles();
		$output = array();
		foreach($data as $role){
			$output[] = array('id'=>$role, 'val'=>$role);
		}
		return $output;
	}
	 	
	/*
	 * ------------------------------------------------------------
	 * PRIVATE STATIC FUNCTIONS
	 * ------------------------------------------------------------
	 */
	
	private static function get_new_salt(){
		$HASH_SALT_LENGTH = 10;
		return substr(sha1(rand()), 0,$HASH_SALT_LENGTH);
	}
	
	private static function encrypt_password($password, $hashsalt){
		$HASH_KEY = "553138c1ffdf5a9fb6e4527ac10df18b759595fe263bfb6d94360ead9d71e668"; //site key
		$HASH_ITERATION = 160; // number of iterations for hash encrypt code
		$HASH_ALGO = "sha256"; //generates a 64-bit hash code
		
		for($i=0;$i<$HASH_ITERATION;$i++){
			$password = hash_hmac($HASH_ALGO, $password . $hashsalt, $HASH_KEY);// iterate the login a number of times
		}
		
		return $password; //return the encrypted password
	}
	 
	
	 
}