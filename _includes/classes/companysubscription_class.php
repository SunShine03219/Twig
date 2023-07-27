<?php

class company_subscription{


	/*
	 * ------------------------------------------------------------
	 * VARIABLES
	 * ------------------------------------------------------------
	 */
	
	private $id;
	private $company_id;
	private $defined_subscription_id;
	private $payment_processor_subscription_id;
	private $payment_processor_name;
	private $ccexp;
	private $ctype;
	private $cclast;
	private $subscription_name;
	private $subscription_interval_length;
	private $subscription_interval_unit;
	private $subscription_payment_amount;
	private $subscription_occurances;
	private $billing_start_date;
	private $subscription_description;
	private $status;
	 
	const TABLE = 'company_subscriptions';
	
	 
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
		$this->company_id = "";
		$this->defined_subscription_id = "";
		$this->payment_processor_subscription_id = "";
		$this->payment_processor_name = "";
		$this->subscription_name = "";
		$this->ccexp = "";
		$this->ctype = "";
		$this->cclast = "";
		$this->subscription_interval_length = "";
		$this->subscription_interval_unit = "";
		$this->subscription_payment_amount = "";
		$this->subscription_occurances = "";
		$this->billing_start_date = "";
		$this->subscription_description = "";
		$this->status = 0;

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
		$this->set_defined_subscription_id($data['defined_subscription_id']);
		$this->set_payment_processor_subscription_id($data['payment_processor_subscription_id']);
		$this->set_payment_processor_name($data['payment_processor_name']);
		$this->set_subscription_name($data['subscription_name']);
		$this->set_ccexp($data['ccexp']);
		$this->set_ctype($data['ctype']);
		$this->set_cclast($data['cclast']);
		$this->set_subscription_interval_length($data['subscription_interval_length']);
		$this->set_subscription_interval_unit($data['subscription_interval_unit']);
		$this->set_subscription_payment_amount($data['subscription_payment_amount']);
		$this->set_subscription_occurances($data['subscription_occurances']);
		$this->set_billing_start_date($data['billing_start_date']);
		$this->set_subscription_description($data['subscription_description']);
		$this->set_status($data['status']);
		
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

	public function get_defined_subscription_id(){
		return $this->defined_subscription_id;
	}

	public function get_payment_processor_subscription_id(){
		return $this->payment_processor_subscription_id;
	}

	public function get_payment_processor_name(){
		return $this->payment_processor_name;
	}

	public function get_subscription_name(){
		return $this->subscription_name;
	}

	public function get_ccexp(){
		return $this->ccexp;
	}

	public function get_ctype(){
		return $this->ctype;
	}

	public function get_cclast(){
		return $this->cclast;
	}

	public function get_subscription_interval_length(){
		return $this->subscription_interval_length;
	}

	public function get_subscription_interval_unit(){
		return $this->subscription_interval_unit;
	}

	public function get_subscription_payment_amount(){
		return $this->subscription_payment_amount;
	}

	public function get_subscription_occurances(){
		return $this->subscription_occurances;
	}

	public function get_billing_start_date(){
		return $this->billing_start_date;
	}

	public function get_subscription_description(){
		return $this->subscription_description;
	}
	
	public function get_status(){
		return $this->status;
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

	public function set_defined_subscription_id($v){
		if($v){
			$this->defined_subscription_id = $v;
		}
	}

	public function set_payment_processor_subscription_id($v){
		if($v){
			$this->payment_processor_subscription_id = $v;
		}
	}

	public function set_payment_processor_name($v){
		if($v){
			$this->payment_processor_name = $v;
		}
	}

	public function set_subscription_name($v){
		if($v){
			$this->subscription_name = $v;
		}
	}

	public function set_ccexp($v){
		if($v){
			$this->ccexp = $v;
		}
	}

	public function set_ctype($v){
		if($v){
			$this->ctype = $v;
		}
	}

	public function set_cclast($v){
		if($v){
			$this->cclast = $v;
		}
	}

	public function set_subscription_interval_length($v){
		$this->subscription_interval_length = $v;
	}

	public function set_subscription_interval_unit($v){
		$this->subscription_interval_unit = $v;
	}

	public function set_subscription_payment_amount($v){
		$this->subscription_payment_amount = $v;
	}

	public function set_subscription_occurances($v){
		$this->subscription_occurances = $v;
	}

	public function set_billing_start_date($v){
		$this->billing_start_date = $v;
	}

	public function set_subscription_description($v){
		if($v){
			$this->subscription_description = $v;
		}
	}
	
	public function set_status($v){
		$this->status = $v;
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
		$myarray['defined_subscription_id'] = $this->defined_subscription_id;
		$myarray['payment_processor_subscription_id'] = $this->payment_processor_subscription_id;
		$myarray['payment_processor_name'] = $this->payment_processor_name;
		$myarray['subscription_name'] = $this->subscription_name;
		$myarray['ccexp'] = $this->ccexp;
		$myarray['ctype'] = $this->ctype;
		$myarray['cclast'] = $this->cclast;
		$myarray['subscription_interval_length'] = $this->subscription_interval_length;
		$myarray['subscription_interval_unit'] = $this->subscription_interval_unit;
		$myarray['subscription_payment_amount'] = $this->subscription_payment_amount;
		$myarray['subscription_occurances'] = $this->subscription_occurances;
		$myarray['billing_start_date'] = $this->billing_start_date;
		$myarray['subscription_description'] = $this->subscription_description;
		$myarray['status'] = $this->status;
		return $myarray;
	}
	
	
	/*
	 * ------------------------------------------------------------
	 * FROM_ARRAY
	 * ------------------------------------------------------------
	 */

	public function from_array(&$data){
		$this->set_id($data['id']);
		$this->set_company_id($data['company_id']);
		$this->set_defined_subscription_id($data['defined_subscription_id']);
		$this->set_payment_processor_subscription_id($data['payment_processor_subscription_id']);
		$this->set_payment_processor_name($data['payment_processor_name']);
		$this->set_subscription_name($data['subscription_name']);
		$this->set_ccexp($data['ccexp']);
		$this->set_ctype($data['ctype']);
		$this->set_cclast($data['cclast']);
		$this->set_subscription_interval_length($data['subscription_interval_length']);
		$this->set_subscription_interval_unit($data['subscription_interval_unit']);
		$this->set_subscription_payment_amount($data['subscription_payment_amount']);
		$this->set_subscription_occurances($data['subscription_occurances']);
		$this->set_billing_start_date($data['billing_start_date']);
		$this->set_subscription_description($data['subscription_description']);
		$this->set_status($data['status']);
	}
	 
	/*
	 * ------------------------------------------------------------
	 * VALIDATE_NEW
	 * ------------------------------------------------------------
	 */
	
	public function validate(&$data){
		$error_flag = false;
		//subscription_name *req'd, unique
		if(empty($data['company_id'])){
			$error_flag = true;
			add_postback_error('company_id', 'Subscription company_id required');
		} else {
			$this->set_subscription_name($data['company_id']);
		}
		
		//subscription_name *req'd, unique
		if(empty($data['defined_subscription_id'])){
			$error_flag = true;
			add_postback_error('defined_subscription_id', 'Subscription defined_subscription_id required');
		} else {
			$this->set_subscription_name($data['defined_subscription_id']);
		}
		
		//subscription_name *req'd, unique
		if(empty($data['payment_processor_subscription_id'])){
			$error_flag = true;
			add_postback_error('payment_processor_subscription_id', 'Subscription payment_processor_subscription_id required');
		} else {
			$this->set_subscription_name($data['payment_processor_subscription_id']);
		}
		
		//subscription_name *req'd, unique
		if(empty($data['payment_processor_name'])){
			$error_flag = true;
			add_postback_error('payment_processor_name', 'Subscription payment_processor_name required');
		} else {
			$this->set_subscription_name($data['payment_processor_name']);
		}
		
		//subscription_name *req'd, unique
		if(empty($data['subscription_name'])){
			$error_flag = true;
			add_postback_error('subscription_name', 'Subscription subscription_name required');
		} else {
			$this->set_subscription_name($data['subscription_name']);
		}
		
		//subscription_name *req'd, unique
		if(empty($data['ccexp'])){
			$error_flag = true;
			add_postback_error('ccexp', 'Subscription ccexp required');
		} else {
			$this->set_subscription_name($data['ccexp']);
		}
		
		
		//subscription_name *req'd, unique
		if(empty($data['ctype'])){
			$error_flag = true;
			add_postback_error('ctype', 'Subscription ctype required');
		} else {
			$this->set_subscription_name($data['ctype']);
		}
		
		//cclast *req'd
		if(empty($data['cclast'])){
			$error_flag = true;
			add_postback_error('cclast', 'Primary cclast required');
		} else {
			$this->set_cclast($data['cclast']);
		}
		//subscription_interval_length *req'd
		if(empty($data['subscription_interval_length'])){
			$error_flag = true;
			add_postback_error('subscription_interval_length', 'Primary subscription_interval_length required');
		} else {
			$this->set_subscription_interval_length($data['subscription_interval_length']);
		}
		//subscription_interval_unit *req'd
		if(empty($data['subscription_interval_unit'])){
			$error_flag = true;
			add_postback_error('subscription_interval_unit', 'Primary subscription_interval_unit required');
		} else {
			$this->set_subscription_interval_unit($data['subscription_interval_unit']);
		}
		//subscription_payment_amount *req'd
		if(empty($data['subscription_payment_amount'])){
			$error_flag = true;
			add_postback_error('subscription_payment_amount', 'Primary subscription_payment_amount required');
		} else {
			$this->set_subscription_payment_amount($data['subscription_payment_amount']);
		}
		//subscription_occurances *req'd
		if(empty($data['subscription_occurances'])){
			$error_flag = true;
			add_postback_error('subscription_occurances', 'Primary subscription_occurances required');
		} else {
			$this->set_subscription_occurances($data['subscription_occurances']);
		}
		//billing_start_date *req'd
		if(empty($data['billing_start_date'])){
			$error_flag = true;
			add_postback_error('billing_start_date', 'Primary billing_start_date required');
		} else {
			$this->set_billing_start_date($data['billing_start_date']);
		}
		
		$this->set_subscription_description($data['subscription_description']);
		$this->set_status($data['status']);
		
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
	/* 
	 *-------------------------------------------------------------
	 * DELETE_ONE
	 *-------------------------------------------------------------
	 */
	public function delete_one() {
		$sql = "DELETE from " . self::TABLE . " WHERE id='" . $this->get_id() . "' ";
		db_delete_bare($sql);//yes, terrible, I know
		
		return true;
	}
	/*
	 * ------------------------------------------------------------
	 * PREPARE NEW
	 * ------------------------------------------------------------
	 */
	public function prepare_new(){
		//Don't think there is anything that I need to add for this in subscription
		
	}
	 
	 	
	/*
	 * ------------------------------------------------------------
	 * PUBLIC MEMBER FUNCTIONS
	 * ------------------------------------------------------------
	 */
	 
	 public function invoke() {
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		
		switch($method){
			case 'post':
				return true;
				break;
				
			case 'get':
			    return $this->get_subscritions();
				break;
				
			default:
				return $this->build_error('Unsupported request method');
		}
	 }
	 
	 public function get_subscritions_by_company($company_id) {
		 $array = db_select(self::TABLE, array('company_id'=>$company_id), "", "*", "", " order by date(billing_start_date) DESC ");
		 return $array;
	 }
	 
	 private function get_subscritions() {
		 $array = db_select(self::TABLE);
		 return $array;
	 }
	 	
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
	 
	
	/*
	 * ------------------------------------------------------------
	 * PRIVATE STATIC FUNCTIONS
	 * ------------------------------------------------------------
	 */
	 	
	
	
	
}