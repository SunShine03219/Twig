<?php

class userControllerException extends Exception{
	public function __construct($message, $code=0, Exception $previous=null){
		parent::__construct($message, $code, $previous);
	}
	public function __toString() {
		return $this->message;
	}
}

class menucontroller extends controller{
	
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
		} catch (userControllerException $e){
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
			return $this->edit_user($id);
		}
		if($action == 'new'){
			return $this->new_user(); 
		}
		if($action == 'toggle' && $id>0){
			return $this->toggle($id);
		}
		if($action == 'reset' && $id>0){
			return $this->reset_password_form($id);
		}
		throw new controllerException('Unsupported Action');
	}
	
	private function retrieve_user($id){
		$id = intval($id);
		$user = new user();
		if($user->make_me_into($id)){
			return $user;
		}
		throw new userControllerException('Unable to retrieve user ' . $id);
	}
	
	private function handle_get_me($action){
		$user = $this->retrieve_user($_SESSION['FT']['user_id']);
		switch ($action){
			case 'reset':
				return $this->build_user_reset_form($user, true);
			case 'edit':
			default:
				return $this->build_user_edit_form($user, true);
		}
	}
	
	private function build_view(){
		$view = new userview();
		$output = $view->invoke();
		//$output->add($this->build_quickinsert());
		return $output;
	}
	
	private function build_quickinsert(){
		$object = new user();
		$form = new formbuilder();
		$object->formbuilder_quick_insert($form);
		return $form;
	}
	
	private function edit_user($id){
		$user = $this->retrieve_user($id);
		if($user->get_company_id() != $_SESSION['FT']['company_id']){
			return $this->build_error('You are not permitted to edit this user');
		}
		return $this->build_user_edit_form($user);
	}
	
	private function reset_password_form($id){
		$user = $this->retrieve_user($id);
		if($user->get_company_id() != $_SESSION['FT']['company_id']){
			return $this->build_error('You are not permitted to edit this user');
		}
		return $this->build_user_reset_form($user);
	}
	
	private function build_user_edit_form(user &$user, $me=false){
		$form = $this->default_form('edit_user');
		$this->add_nonce($form, array('id'=>($me?'me':$user->get_id()), 'action'=>'update', 'company_id'=>$_SESSION['FT']['company_id']));
		$user->formbuilder_edit($form);
		if($me){
			$form->add_after($this->build_user_reset_form($user, $me));
		}
		return $form;
	}
	
	private function new_user(){
		$user = new user();
		return $this->build_user_new_form($user);
	}
	
	private function build_user_new_form(user &$user){
		$form = $this->default_form('new_user');
		$this->add_nonce($form, array('action'=>'insert', 'company_id'=>$_SESSION['FT']['company_id']));
		$user->formbuilder_new($form);
		return $form;
	}
	
	private function build_user_reset_form(user &$user, $me=false){
		$form = $this->default_form('reset_password');
		$this->add_nonce($form, array('id'=>($me?'me':$user->get_id()), 'action'=>'reset'));
		$user->formbuilder_password_reset($form);
		return $form;
	}
	
	private function toggle($id){
		$user = $this->retrieve_user($id);
		if($user->get_company_id() != $_SESSION['FT']['company_id']){
			throw new userControllerException('You are not permitted to change this user.');
		}
		$user->toggle();
		if(!$user->update_one()){
			throw new userControllerException('Unable to save your changes.');
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
			return $this->update_user($id);
		}
		if($action == 'toggle' && $id>0){
			return $this->toggle($id);
		}
		throw new controllerException('Unsupported Action');
	}
	
	private function handle_post_me($action){
		$user = $this->retrieve_user($_SESSION['FT']['user_id']);
		switch($action){
			case 'reset':
				return $this->reset_password($user, true);
				break;
			case 'update':
				return $this->update($user, true);
				break;
		}
		throw new controllerException('Unsupported Action');
	}
	
	private function insert(){
		$this->test_nonce();
		$user = new user();
		if(!$user->validate_new($this->request->get_data())){
			return $this->build_user_new_form($user);
		}
		if(!$user->insert_new()){
			throw new userControllerException('Unable to save new user account');
		}
		return $this->build_view();
	}
	
	private function do_reset_password($id){
		$user = $this->retrieve_user($id);
		return $this->reset_password($user);
	}
	
	private function reset_password(user &$user, $me = false){
		$this->test_nonce();
		if(!$user->validate_password($this->request->get_data())){
			return $this->build_user_reset_form($user, $me);
		}
		if(!$user->update_password()){
			throw new userControllerException('Unable to save new password');
		}
		if($me){
			return $this->build_user_edit_form($user, $me);
		}
		return $this->build_view();
	}
	
	private function update_user($id){
		$user = $this->retrieve_user($id);
		return $this->update($user);
	}
	
	private function update(user &$user, $me=false){
		$this->test_nonce();
		if(!$user->validate_edit($this->request->get_data())){
			return $this->build_user_edit_form($user, $me);
		}
		if(!$user->update_one()){
			throw new userControllerException('Unable to save your settings');
		}
		if($me){
			return $this->build_user_edit_form($user, $me);
		}
		return $this->build_view();
	}
	
}