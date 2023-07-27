<?php
class logincontroller{
	
	private $errors;
	
	public function __construct(){
		$this->errors = array();
	}
	
	public function invoke(){
		$request = new genericrequest();
		switch($request->get_mode()){
			case 'get':
				return $this->loginform($request);
				break;
			case 'post':
				$request->add_array($_POST);
				return $this->login($request);
				break;
			default:
				return $this->build_error('Unsupported Request');
		}
	}
	

	private function build_error($message){
		$buildable = new basicbuildable();
		$buildable->add_content($message, 'error');
		return $buildable;
	}
	
	private function login(genericrequest &$request){
// 		if(!$this->test_nonce($request->get_data())){
// 		    return 'Invalid security data in request';
// 			//return $this->build_error('Invalid security data in request');
// 		}
		
		$this->clean_request($request);
		if($this->validate_request($request)){
			$data = array('username'=>$request->username, 'password'=>$request->password);
			$user = new user();
			$user->validate_login($data);
			$result = $user->attempt();
			if($result){

				$where = array('id' => $user->get_company_id());
				$data = ['username' => $user->get_username(),'last_login' => $today = date("Y-m-d H:i:s")];
				$result = db_update("company", $data, $where);

				$this->login_success($user);
				$this->send_to_destination();
			} else {
				//set_postback_msg('Invalid Login. Please review your entries below.');
				//return $this->loginform($request);
			    return 'Invalid Login. Please review your entries below.';
			}
		} else {
			//set_postback_msg('Invalid Login. Please review your entries below.');
			//return $this->loginform($request);
		    return 'Invalid Login. Please review your entries below.';
		}
	}
	
	private function clean_request(genericrequest &$request){
		$request->username = htmlspecialchars(trim($request->username));
		$request->password = trim($request->password);
	}
	
	private function validate_request(genericrequest &$request){
		$this->validate_username($request->username);
		$this->validate_password($request->password);
		return empty($this->errors);
	}
		
	private function validate_username($username){
		if(empty($username)){
			$this->errors['username'] = 'Please enter your username';
			return false;
		}
		return true;
	}
	
	private function validate_password($password){
		if(empty($password)){
			$this->errors['password'] = 'Please enter your password';
			return false;
		}
		return true;
	}
	
	private function loginform(genericrequest &$request){
		$user = new user();
		$user->set_username($request->username);
		
		if(!empty($this->errors)){
			$_SESSION['FT']['postback_errors'] = $this->errors;
		}
		
		$form = $user->formbuilder_login();
		$nonce = new nonce();
		$nonce->add('type', 'login');
		$nonce->save();
		$form->add_hidden_array($nonce->get_nonce_list());
		return $form;
	}
	
	private function test_nonce($request){
		if(empty($request['nonce'])){
			return false;
		}
		$nonce = new nonce($request['nonce']);
		return $nonce->test_request($request);
	}
	
	private function login_success(user $user){
		$_SESSION['FT']['ds_is_logged_in'] = true;
		$_SESSION['FT']['company_id'] = $user->get_company_id();
		$_SESSION['FT']['user_id'] = $user->get_id();
		$_SESSION['FT']['name_first'] = $user->get_name_first();
		$_SESSION['FT']['name_last'] = $user->get_name_last();
		$_SESSION['FT']['position'] = $user->get_position();
		$affiliation = $user->get_affiliation();
		$_SESSION['FT']['affiliation'] = array();
		$company = new company();
		
		if(is_array(($_SESSION['FT']['company_id']) ? !in_array($_SESSION['FT']['company_id'], (is_array($affiliation) ? $affiliation : array($affiliation))) : $_SESSION['FT']['company_id']) == $affiliation) {
		/*
		$companyId = isset($_SESSION['FT']['company_id']) ? $_SESSION['FT']['company_id'] : null;
		$checkAffiliation = false;
		if(is_array($companyId)){
		    $checkAffiliation = !in_array($companyId, $affiliation);
		}else{
		    $checkAffiliation = ($companyId == $affiliation) ? true : false;
		}
		
		if($checkAffiliation) {    
		*/
			$company->make_me_into($_SESSION['FT']['company_id']);
			$_SESSION['FT']['affiliation'][] = $company->to_array();
		}
		if(is_array($affiliation)) {
			foreach($affiliation as $item) {
				$company->make_me_into($item);
				$_SESSION['FT']['affiliation'][] = $company->to_array();
			}
		}
		$_SESSION['FT']['SEC_SALES'] = ($user->get_sec_sales()?true:false);
		$_SESSION['FT']['SEC_SERVICE'] = ($user->get_sec_service()?true:false);
		$_SESSION['FT']['SEC_MANAGER'] = ($user->get_sec_manager()?true:false);
		$_SESSION['FT']['SEC_ADMIN'] = ($user->get_sec_admin()?true:false);
		$_SESSION['FT']['SEC_SUPPORT'] = ($user->get_sec_support()?true:false);
		$_SESSION['FT']['SEC_DELETE'] = ($user->get_sec_delete()?true:false);
		$_SESSION['FT']['SEC_DEALS_PERMISSION'] = ($user->get_sec_deals_permission()?true:false);

		unset($_SESSION['open_reg']);
	}
	
	private function send_to_destination(){
		//$destination = "deals.php?action=unfunded";
		$destination = "/deals";
		
		if(isset($_SESSION['FT']['redir'])){
			$destination = $_SESSION['FT']['redir'];
			unset($_SESSION['FT']['redir']);
		}
		
		header('location: ' . $destination);
		exit();
	}
	
	public function force_login(user &$user){
		$this->login_success($user);
		$this->send_to_destination();
	}
	
}