<?php

class couponControllerException extends Exception{
	public function __construct($message, $code=0, Exception $previous=null){
		parent::__construct($message, $code, $previous);
	}
	public function __toString() {
		return $this->message;
	}
}

class couponcontroller extends controller{

	public function getTableList($request, $status, $company_id) {
        $primaryKey = 'id';

        $columns = array(
            array( 'db' => 'title', 'dt' => 0 ),
            array( 'db' => 'start_date', 'dt' => 1 , 
					'formatter' => function($d, $row){
						if(!empty($d) && $d != '0000-00-00'){
							if($time = strtotime($d)){
								return date('m/d/Y', $time);
							}
						}
						return '';
					}),
            array( 'db' => 'user_used', 'dt' => 2 ),
            array( 'db' => 'coupon_value', 'dt' => 3 ),
            array( 'db' => 'status', 'dt' => 4 ),
            array( 'db' => 'id',     'dt' => 5 ),
            array( 'db' => 'end_date', 'dt' => 6 , 
				'formatter' => function($d, $row){
					if(!empty($d) && $d != '0000-00-00'){
						if($time = strtotime($d)){
							return date('m/d/Y', $time);
						}
					}
					return '';
				}),
            array( 'db' => 'coupon_type', 'dt' => 7 ),
            // array( 'dt' => 5 )
        );
        //$whereStatement = "";
        $whereStatement = "title != ''";
        if($status == "active") {
            $whereStatement .= " AND status = 1";
        } else if($status == "inactive") {
            $whereStatement .= " AND status = 0";
        }
        return json_encode(
            SSP::complex( $request, 'coupons', $primaryKey, $columns, null, $whereStatement)
        );

    }
	
	protected function execute(){
		try{
			switch($this->request->get_mode()){
				case 'post':
					$this->request->add_array($_POST);
					return $this->handle_post();
					break;
				case 'get':
					$this->request->add_array($_GET);
					return $this->handle_get();
					break;
				case 'default':
					throw new controllerException('Unsupported Request Method');
			}
		} catch (couponControllerException $e){
			return $this->build_error((string)$e);
		}
		throw new controllerException('Fell out of execute');
	}
	
	private function handle_get(){
		$action = trim(urldecode($this->request->action));
		$id = trim(urldecode($this->request->id));
		if($id == 'me'){
			return $this->handle_get_me($action);
		}
		$id = intval($id);
		if((empty($action) && empty($id))||($action == 'view')){
			return $this->build_view();
		}
		if((empty($action) || ($action=='edit')) && $id>0){
			return $this->edit_coupon($id);
		}
		if($action == 'new'){
			return $this->new_edit_coupon();
		}
		if($action == 'toggle' && $id>0){
			return $this->toggle($id);
		}
		if($action == 'reset' && $id>0){
			return $this->reset_password_form($id);
		}
		throw new controllerException('Unsupported Action');
	}
	
	private function retrieve_coupon($id){
		$id = intval($id);
		$coupon = new coupon();
		if($coupon->make_me_into($id)){
			return $coupon;
		}
		throw new couponControllerException('Unable to retrieve coupon ' . $id);
	}
	
	private function handle_get_me($action){
		$coupon = $this->retrieve_coupon($_SESSION['FT']['coupon_id']);
		switch ($action){
			case 'reset':
				return $this->build_coupon_reset_form($coupon, true);
			case 'edit':
			default:
				return $this->build_coupon_edit_form($coupon, true);
		}
	}
	
	private function build_view(){
		$view = new couponview();
		$output = $view->invoke();
		//$output->add($this->build_quickinsert());
		return $output;
	}
	
	private function build_quickinsert(){
		$object = new coupon();
		$form = new formbuilder();
		$object->formbuilder_quick_insert($form);
		return $form;
	}
	
	private function edit_coupon($id){
		$coupon = $this->retrieve_coupon($id);
		if($coupon->get_company_id() != $_SESSION['FT']['company_id']){
			return $this->build_error('You are not permitted to edit this coupon');
		}
		return $this->build_coupon_edit_form($coupon);
	}
	
	private function reset_password_form($id){
		$coupon = $this->retrieve_coupon($id);
		if($coupon->get_company_id() != $_SESSION['FT']['company_id']){
			return $this->build_error('You are not permitted to edit this coupon');
		}
		return $this->build_coupon_reset_form($coupon);
	}
	
	private function build_coupon_edit_form(coupon &$coupon, $me=false){
		$form = $this->default_form('edit_coupon');
		$this->add_nonce($form, array('id'=>($me?'me':$coupon->get_id()), 'action'=>'update', 'company_id'=>$_SESSION['FT']['company_id']));
		$coupon->formbuilder_edit($form);
		if($me){
			$form->add_after($this->build_coupon_reset_form($coupon, $me));
		}
		return $form;
	}
	
	private function new_coupon(){
		$coupon = new coupon();
		return $this->build_coupon_new_form($coupon);
	}
	
	private function build_coupon_new_form(coupon &$coupon){
		$form = $this->default_form('new_coupon');
		$this->add_nonce($form, array('action'=>'insert', 'company_id'=>$_SESSION['FT']['company_id']));
		$coupon->formbuilder_new($form);
		return $form;
	}
	
	private function build_coupon_reset_form(coupon &$coupon, $me=false){
		$form = $this->default_form('reset_password');
		$this->add_nonce($form, array('id'=>($me?'me':$coupon->get_id()), 'action'=>'reset'));
		$coupon->formbuilder_password_reset($form);
		return $form;
	}
	
	private function toggle($id){
		$coupon = $this->retrieve_coupon($id);
		if($coupon->get_company_id() != $_SESSION['FT']['company_id']){
			throw new couponControllerException('You are not permitted to change this coupon.');
		}
		$coupon->toggle();
		if(!$coupon->update_one()){
			throw new couponControllerException('Unable to save your changes.');
		}
		return $this->build_view();
	}
	
	private function handle_post(){
		$action = trim($this->request->action);
		$id = trim($this->request->id);
		
		if($id == 'me'){
			return $this->handle_post_me($action);
		}
		
		$id = intval($id);
		
		if($action == 'insert'){
			return $this->insert();
		}
		if($action == 'reset' && $id>0){
			return $this->do_reset_password($id);
		}
		if(($action == 'update' || empty($action)) && $id>0){
			return $this->update_coupon($id);
		}
		if($action == 'toggle' && $id>0){
			return $this->toggle($id);
		}
		throw new controllerException('Unsupported Action');
	}
	
	private function handle_post_me($action){
		$coupon = $this->retrieve_coupon($_SESSION['FT']['coupon_id']);
		switch($action){
			case 'reset':
				return $this->reset_password($coupon, true);
				break;
			case 'update':
				return $this->update($coupon, true);
				break;
		}
		throw new controllerException('Unsupported Action');
	}
	
	private function insert(){
		$this->test_nonce();
		$coupon = new coupon();
		if(!$coupon->validate_new($this->request->get_data())){
			return $this->build_coupon_new_form($coupon);
		}
		if(!$coupon->insert_new()){
			throw new couponControllerException('Unable to save new coupon account');
		}
		return $this->build_view();
	}
	
	private function do_reset_password($id){
		$coupon = $this->retrieve_coupon($id);
		return $this->reset_password($coupon);
	}
	
	private function reset_password(coupon &$coupon, $me = false){
		$this->test_nonce();
		if(!$coupon->validate_password($this->request->get_data())){
			return $this->build_coupon_reset_form($coupon, $me);
		}
		if(!$coupon->update_password()){
			throw new couponControllerException('Unable to save new password');
		}
		if($me){
			return $this->build_coupon_edit_form($coupon, $me);
		}
		return $this->build_view();
	}
	
	private function update_coupon($id){
		$coupon = $this->retrieve_coupon($id);
		return $this->update($coupon);
	}
	
	private function update(coupon &$coupon, $me=false){
		$this->test_nonce();
		if(!$coupon->validate_edit($this->request->get_data())){
			return $this->build_coupon_edit_form($coupon, $me);
		}
		if(!$coupon->update_one()){
			throw new couponControllerException('Unable to save your settings');
		}
		if($me){
			return $this->build_coupon_edit_form($coupon, $me);
		}
		return $this->build_view();
	}

    public function generateRandomString($length = 25) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function validateCoupon($coupon) {
    	$couponEndData = $coupon['end_date'];
    	$currentDate = date("Y-m-d");
    	if($coupon['no_limit'] == 0){
    		if($coupon['user_used'] >= $coupon['user_allowed']){
    			return false;
    		}
    	}
    	if($currentDate <= $couponEndData){
    		return true;
    	}
    	return false;
    }
	
}