<?php

class subscription{


	/*
	 * ------------------------------------------------------------
	 * VARIABLES
	 * ------------------------------------------------------------
	 */
	
	private $id;
	private $name;
	private $referral;
	private $payment_interval_length;
	private $payment_interval_unit;
	private $payment_amount;
	private $total_occurances;
	private $trial_days;
	private $description;
	private $active;
	private $type;
	 
	const TABLE = 'defined_subscriptions';
	
	 
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
		$this->referral = "";
		$this->payment_interval_length = "";
		$this->payment_interval_unit = "";
		$this->payment_amount = "";
		$this->total_occurances = "";
		$this->trial_days = "";
		$this->description = "";
		$this->active = 0;
		$this->type="";

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
		$this->set_referral($data['referral']);
		$this->set_payment_interval_length($data['payment_interval_length']);
		$this->set_payment_interval_unit($data['payment_interval_unit']);
		$this->set_payment_amount($data['payment_amount']);
		$this->set_total_occurances($data['total_occurances']);
		$this->set_trial_days($data['trial_days']);
		$this->set_description($data['description']);
		$this->set_active($data['active']);
		$this->set_type($data['type']);
		
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

	public function get_referral(){
		return $this->referral;
	}

	public function get_payment_interval_length(){
		return $this->payment_interval_length;
	}

	public function get_payment_interval_unit(){
		return $this->payment_interval_unit;
	}

	public function get_payment_amount(){
		return $this->payment_amount;
	}

	public function get_total_occurances(){
		return $this->total_occurances;
	}

	public function get_trial_days(){
		return $this->trial_days;
	}

	public function get_description(){
		return $this->description;
	}
	
	public function get_active(){
		return $this->active;
	}
	public function get_type(){
		return $this->type;
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

	public function set_referral($v){
		if($v){
			$this->referral = $v;
		}
	}

	public function set_payment_interval_length($v){
		$this->payment_interval_length = $v;
	}

	public function set_payment_interval_unit($v){
		$this->payment_interval_unit = $v;
	}

	public function set_payment_amount($v){
		$this->payment_amount = $v;
	}

	public function set_total_occurances($v){
		$this->total_occurances = $v;
	}

	public function set_trial_days($v){
		$this->trial_days = $v;
	}

	public function set_description($v){
		if($v){
			$this->description = $v;
		}
	}
	public function set_type($type){
		if($type){
			$this->type = $type;
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

	public function to_array(){
		$myarray = array();
		$myarray['id'] = $this->id;
		$myarray['name'] = $this->name;
		$myarray['referral'] = $this->referral;
		$myarray['payment_interval_length'] = $this->payment_interval_length;
		$myarray['payment_interval_unit'] = $this->payment_interval_unit;
		$myarray['payment_amount'] = $this->payment_amount;
		$myarray['total_occurances'] = $this->total_occurances;
		$myarray['trial_days'] = $this->trial_days;
		$myarray['description'] = $this->description;
		$myarray['active'] = $this->active;
		$myarray['type'] = $this->type;
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
		$this->set_referral($data['referral']);
		$this->set_payment_interval_length($data['payment_interval_length']);
		$this->set_payment_interval_unit($data['payment_interval_unit']);
		$this->set_payment_amount($data['payment_amount']);
		$this->set_total_occurances($data['total_occurances']);
		$this->set_trial_days($data['trial_days']);
		$this->set_description($data['description']);
		$this->set_active($data['active']);
		$this->set_type($data['type']);
	}
	 
	/*
	 * ------------------------------------------------------------
	 * VALIDATE_NEW
	 * ------------------------------------------------------------
	 */
	
	public function validate(&$data){
		$error_flag = false;
		//name *req'd, unique
		if(empty($data['name'])){
			$error_flag = true;
			add_postback_error('name', 'Subscription name required');
		} else {
			$this->set_name($data['name']);
		}
		
		//referral *req'd
		if(empty($data['referral'])){
			$error_flag = true;
			add_postback_error('referral', 'Primary referral required');
		} else {
			$this->set_referral($data['referral']);
		}
		//payment_interval_length *req'd
		if(empty($data['payment_interval_length'])){
			$error_flag = true;
			add_postback_error('payment_interval_length', 'Primary payment_interval_length required');
		} else {
			$this->set_payment_interval_length($data['payment_interval_length']);
		}
		//payment_interval_unit *req'd
		if(empty($data['payment_interval_unit'])){
			$error_flag = true;
			add_postback_error('payment_interval_unit', 'Primary payment_interval_unit required');
		} else {
			$this->set_payment_interval_unit($data['payment_interval_unit']);
		}
		//payment_amount *req'd
		if(empty($data['payment_amount'])){
			$error_flag = true;
			add_postback_error('payment_amount', 'Primary payment_amount required');
		} else {
			$this->set_payment_amount($data['payment_amount']);
		}
		//total_occurances *req'd
		if(empty($data['total_occurances'])){
			$error_flag = true;
			add_postback_error('total_occurances', 'Primary total_occurances required');
		} else {
			$this->set_total_occurances($data['total_occurances']);
		}
		//trial_days *req'd
		if(empty($data['trial_days'])){
			$error_flag = true;
			add_postback_error('trial_days', 'Primary trial_days required');
		} else {
			$this->set_trial_days($data['trial_days']);
		}
		
		$this->set_description($data['description']);
		$this->set_active($data['active']);
		$this->set_type($data['type']);
		
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
	 * Formbuilders - public
	 * ------------------------------------------------------------
	 */
	
	public function formbuilder_quick_insert(formbuilder &$form){
		$form->start_section('form_row');
		$form->add_subtitle('New Subscription');
		$form->end_section();
	
		$form->start_section('form_row');
		$form->add_custom('<a href="subscription.php?action=new">Create New</a>', 'Create new subscription');
		$form->end_section();
	}
	
	/*
	 * ------------------------------------------------------------
	 * Formbuilders - private
	 * ------------------------------------------------------------
	 */
	 
	 private function formbuilder_id_hidden(&$form){
	 	$form->add_hidden('id', $this->get_id());
	 }
	 
	 private function formbuilder_name(&$form, $editable = 1){
	 	$form->add_text('name', 'Subscription name', $this->get_name(), 1, $editable);
	 }
	 
	 private function formbuilder_referral(&$form, $editable = 1){
	 	$form->add_text('referral', 'Referral Code', $this->get_referral(), 1, $editable);
	 }
	 
	 private function formbuilder_payment_interval_length(&$form, $editable = 1){
	 	$form->add_text('payment_interval_length', 'Interval Length', $this->get_payment_interval_length(), 0, $editable);
	 }
	 
	 private function formbuilder_payment_interval_unit(&$form, $editable = 1){
	 	$form->add_text('payment_interval_unit', 'Interval Unit', $this->get_payment_interval_unit(), 0, $editable);
	 }
	 
	 private function formbuilder_payment_amount(&$form, $editable = 1){
	 	$form->add_text('payment_amount', 'Payment Amount', $this->get_payment_amount(), 0, $editable);
	 }
	 
	 private function formbuilder_total_occurances(&$form, $editable = 1){
	 	$form->add_text('total_occurances', 'total_occurances', $this->get_total_occurances(), 0, $editable);
	 }
	 
	 private function formbuilder_trial_days(&$form, $editable = 1){
	 	$form->add_text('trial_days', 'Trial Days', $this->get_trial_days(), 0, $editable);
	 }
	 
	 private function formbuilder_description(&$form, $editable = 1){
	 	$form->add_text('description', 'Description', $this->get_description(), 1, $editable);
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
	 
	 public function get_active_subscritions() {
		 
		 $array = db_select(self::TABLE, ['active' => 1]);
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