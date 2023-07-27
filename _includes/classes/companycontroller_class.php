<?php
class companyControllerException extends Exception{
	public function __construct($message, $code=0, Exception $previous=null){
		parent::__construct($message, $code, $previous);
	}
	public function __toString() {
		return $this->message;
	}
}

class companycontroller extends controller{
	
	protected function execute(){
		try{
			$mode = $this->request->get_mode();
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
		} catch (companyControllerException $e){
			return $this->build_error((string)$e);
		}
		throw new controllerException('Fell out of execute');
	}

	public function getTableList($request) {
		$primaryKey = 'id';

		$columns = array(
			array( 'db' => 'name', 'dt' => 0 ),
			array( 'db' => 'contact',  'dt' => 1 ),
			array( 'db' => 'phone_main',     'dt' => 2 ),
			array( 'db' => 'phone_contact',  'dt' => 3 ),
			array( 'db' => 'username', 'dt' => 4 ),
			array( 'db' => 'last_login',  'dt' => 5 ),
			array( 'db' => 'active',   'dt' => 6 ),
			array( 'db' => 'id',     'dt' => 7 ),
			// array( 'dt' => 5 )
		);
		$whereStatement = "active = 1";
		return json_encode(
			SSP::complex( $request, 'company', $primaryKey, $columns, null, $whereStatement)
		);

	}

	public function getCustomersBillsTableList($request) {
		$primaryKey = 'id';

		$columns = array(
			array( 'db' => 'name', 'dt' => 0 ),
			array( 'db' => 'contact',  'dt' => 1 ),
			array( 'db' => 'phone_main',     'dt' => 2 ),
			array( 'db' => 'phone_contact',  'dt' => 3 ),
			array( 'db' => 'active',   'dt' => 4 ),
			array( 'db' => 'username', 'dt' => 5 ),
			array( 'db' => 'last_login',  'dt' => 6 , 
				'formatter' => function($d, $row) {
					return $d == "0000-00-00 00:00:00" ? "" : $d;
				}),
			// array( 'db' => 'id',     'dt' => 7 ),
			// array( 'dt' => 5 )
		);
		$whereStatement = "active = 1";
		return json_encode(
			SSP::complex( $request, 'company', $primaryKey, $columns, null, $whereStatement)
		);

	}
	
	private function retrieve_company($id){
		$id = intval($id);
		$company = new company();
		if($company->make_me_into($id)){
			return $company;
		}
		throw new companyControllerException('Unable to retrieve company ' . $id);
	}
	
	private function retrieve_subscription($company_id){
		$id = intval($company_id);
		$subscription = new companysubscription();
		$subscription->make_me_into_company($id);
		return $subscription;
	}
	
	private function handle_post(){
		$action = trim($this->request->action);
		$id = trim($this->request->id);
		if($id == 'me'){
			return $this->handle_post_me($action);
		}
		$id = intval($id);
		if($action == 'insert'){
			return $this->insert_company();
		}
		if($action == 'update' && $id>0){
			return $this->update_company($id);
		}
		if($action == 'subscription' && $id>0){
			return $this->update_subscription_company_id($id);
		}
		if($action == 'ccsetup' && $id>0){
			return $this->update_credit_card($id);
		}
		throw new controllerException('Unsupported Action');
	}
	
	private function handle_post_me($action){
		$company = $this->retrieve_company($_SESSION['FT']['company_id']);
		switch($action){
			case 'update':
				return $this->update($company, true);
				break;
			case 'subscription':
				return $this->update_subscription($company, true);
				break;
			case 'ccsetup':
				return $this->update_credit_card($company->get_id(), true);
				break;
			
		}
		throw new controllerException('Unsupported Action');
	}
	
	private function update_credit_card($company_id, $me = false){
		$this->test_nonce();
		$subscription = $this->retrieve_subscription($company_id);
		if(!$subscription->get_defined_subscription_id()){
			//shortcut in case the company doesn't have a subscription active
			return $this->build_company_change_subscription($subscription, $me);
		}
		$cc = new creditcard();
		if(!$cc->validate_new($this->request->get_data())){
			return $this->credit_card_form($company_id, $me);
		}
		if($subscription->enrolled_in_ARB()){
			$result = $subscription->change_cc($cc);
		} else {
			$result = $subscription->activate($cc);
		}
		
		if(!$result){
			return $this->credit_card_form($company_id);
		}
		
		set_success_msg('Credit Card Updated');
		$company = $this->retrieve_company($company_id);
		return $this->build_company_edit_form($company, $me);
	}
	
	private function update_subscription_company_id($company_id){
		$company = $this->retrieve_company($company_id);
		return $this->update_subscription($company);
	}
	
	private function update_subscription(company &$company, $me = false){
		$this->test_nonce();
		$subscription = $this->retrieve_subscription($company->get_id());
		$pre_subscription_defined_id = $subscription->get_defined_subscription_id();
		if(!$subscription->validate_change($this->request->get_data())){
			return $this->build_company_change_subscription($subscription, $me);
		}
		if($pre_subscription_defined_id != $subscription->get_defined_subscription_id()){
			//subscription changed, defaults loaded from template
			if($subscription->enrolled_in_ARB()){
				if(!$subscription->updatepriceARB()){
					return $this->build_company_change_subscription($subscription, $me);
				}
			}
			
			//at this point the companysubscription might be new, best way to test is with running a test on the id
			if($subscription->get_id() > 0){
				$subscription->update_one();
			} else {
				$subscription->insert_new();
			}
			return $this->build_company_edit_form($company, $me);
		} else {
			//no change
			return $this->build_company_edit_form($company, $me);
		}
	}
	
	private function insert_company(){
		$company = new company();
		return $this->insert($company);
	}
	
	private function insert(company &$company){
		$this->test_nonce();
		if(!$company->validate_new($this->request->get_data())){
			return $this->build_company_new_form($company);
		}
		if(!$company->insert_new()){
			throw new companyControllerException('Unable to save new company');
		}
		return $this->build_view();
	}
	
	private function update_company($id){
		$company = $this->retrieve_company($id);
		return $this->update($company);
	}
	
	private function update(company &$company, $me=false){
		$this->test_nonce();
		if(!$company->validate_edit($this->request->get_data())){
			return $this->build_company_edit_form($company, $me);
		}
		if(!$company->update_one()){
			throw new companyControllerException('Unable to save your company settings');
		}
		if($me){
			return $this->build_company_edit_form($company, $me);
		}
		return $this->build_view();
	}
	
	private function handle_get(){
		$action = trim(urldecode($this->request->action));
		$id = trim(urldecode($this->request->id));
		if($id == 'me'){
			return $this->handle_get_me($action);
		}
		$id = intval($id);
		
		if((empty($action) && empty($id)) || $action == 'view'){
			return $this->build_view();
		}
		if((empty($action) || $action=='edit') && $id>0){
			return $this->edit_company($id);
		}
		if($action == 'new'){
			return $this->new_company();
		}
		if($action == 'subscription' && $id>0){
			return $this->build_change_subscription($id);
		}
		if($action == 'ccsetup' && $id>0){
			return $this->credit_card_form($id);
		}
		throw new controllerException('Unsupported Action');
	}
	
	private function handle_get_me($action){
		$company = $this->retrieve_company($_SESSION['FT']['company_id']);
		switch($action){
			case 'subscription':
				return $this->build_change_subscription_me($company->get_id());
				break;
			case 'ccsetup':
				return $this->credit_card_form($company->get_id(), true);
				break;
			case 'edit':
			default:
				return $this->build_company_edit_form($company, true);
		}
	}
	
	private function credit_card_form($company_id, $me = false){
		$subscription = $this->retrieve_subscription($company_id);
		if(!$subscription->get_defined_subscription_id()){
			//shortcut in case the company doesn't have a subscription active
			return $this->build_company_change_subscription($subscription, $me);
		}
		$cc = new creditcard();
		$form = $this->default_form('setup_credit_card');
		$this->add_nonce($form, array('id'=>($me?'me':$company_id), 'action'=>'ccsetup'));
		$cc->formbuilder_new($form);
		$form->add_submit();
		return $form;
	}
	
	private function edit_company($id){
		$company = $this->retrieve_company($id);
		return $this->build_company_edit_form($company);
	}
	
	private function build_change_subscription($id){
		$subscription = $this->retrieve_subscription($id);
		return $this->build_company_change_subscription($subscription);
	}
	private function build_change_subscription_me($company_id){
		$subscription = $this->retrieve_subscription($company_id);
		return $this->build_company_change_subscription($subscription, true);
	}
	private function build_company_change_subscription(companysubscription &$subscription, $me = false){
		$form = $this->default_form('change_subscription');
		$this->add_nonce($form, array('action'=>'subscription', 'id'=>($me?'me':$subscription->get_company_id())));
		$subscription->formbuilder_change_subscription($form);
		return $form;
	}
	
	private function build_company_edit_form(company &$company, $me = false){
		$form = $this->default_form('edit_company');
		$this->add_nonce($form, array('action'=>'update', 'id'=>($me?'me':$company->get_id())));
		$company->formbuilder_edit($form);
		
		$subscription = $this->retrieve_subscription($company->get_id());
		// $form->add_after($subscription->display_subscription_details());
		
		return $form;
	}
	
	private function build_company_new_form(company &$company){
		$form = $this->default_form('new_company');
		$this->add_nonce($form, array('action'=>'insert'));
		$company->formbuilder_new($form);
		return $form;
	}
	
	private function new_company(){
		$company = new company();
		return $this->build_company_new_form($company);
	}
	
	private function build_view(){
		$view = new companyview();
		$output = $view->invoke();
		//$output->add($this->build_quickinsert());
		return $output;
	}
	
	private function build_quickinsert(){
		$object = new company();
		$form = new formbuilder();
		$object->formbuilder_quick_insert($form);
		return $form;
	}
}