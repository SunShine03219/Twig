<?php
class toggleControllerException extends Exception{
	public function __construct($message, $code=0, Exception $previous=null){
		parent::__construct($message, $code, $previous);
	}
	public function __toString() {
		return $this->message;
	}
}

class togglecontroller extends controller{
	
	private $type;
	
	public function __construct($type){
		$this->type = $type;
		parent::__construct();
	}

	public function getTableList($request) {
		$primaryKey = 'id';
		$classname = $this->type;
		
		$columns = array(
			array( 'db' => 'name', 'dt' => 0 ),
			array( 'db' => 'active',  'dt' => 1 ),
			array( 'db' => 'id',   'dt' => 2 )
			// array('dt' => 3 )
		);
		$whereStatement = "company_id = {$_SESSION['FT']['company_id']}";
		$status = $_SESSION['FT'][$classname . '_dropdown'];
		if($status == "active") {
			$whereStatement .= " AND active = 1";
		} else if($status == "inactive") {
			$whereStatement .= " AND active = 0";
		}
		if($this->type == "salespeople") {
			$classname = "sales";
		}
		return json_encode(
			SSP::complex( $request, $classname::TABLE, $primaryKey, $columns, null, $whereStatement)
		);

	}
	
	private function get_object($id = null){
		$name = $this->type;
		$object = new $name();
		if($id > 0){
			if(!$object->make_me_into($id)){
				throw new toggleControllerException('Unable to retrieve your ' . $this->type . ' ' . $id);
			}
			if($object->get_company_id() != $_SESSION['FT']['company_id']){
				throw new toggleControllerException('This ' . $this->type . ' is not owned by your company.');
			}
		}
		return $object;
	}
	
	protected function execute(){
		try{
			switch($this->request->get_mode()){
				case 'get':
					$this->request->add_array($_GET);
					return $this->handle_get();
					break;
				case 'post':
					$this->request->add_array($_POST);
					return $this->handle_post();
					break;
				default:
					return $this->build_error('Unsupported Request');
			}
		} catch (toggleControllerException $e){
			return $this->build_error((string)$e);
		}
		throw new controllerException('Fell out of execute');
	}
	
	private function handle_get(){
		$action = trim(urldecode($this->request->action));
		$id = intval(urldecode($this->request->id));
		
		if((empty($action) && empty($id)) || ($action == 'view')){
			return $this->build_view();
		}
		if($action == 'toggle' && $id>0){
			return $this->toggle($id);
		}
		if($action == 'new'){
			return $this->action_form_new();
		}
		if(!empty($id) || (!empty($id) && action=='edit') ){
			return $this->action_form_edit($id);
		}
		return $this->build_error('Unable to work on your request');
	}
	
	private function handle_post(){
		$action = trim($this->request->action);
		$id = intval($this->request->id);
		
		if($action == 'quick' || $action == 'insert'){
			$this->request->active = 'active'; //hack to add active on quick insert;
			return $this->insert();
		}
		if($action == 'update' && !empty($id)){
			return $this->update($id);
		}
		if(action == 'toggle' && !empty($id)){
			return $this->toggle($id);
		}
		return $this->build_error('Unable to work on your request');
	}
	
	private function build_view(){
		$view = new toggleview($this->type);
		$output = $view->invoke();
		//$output->add($this->build_quickinsert());
		return $output;	
	}
	
	private function build_quickinsert(){
		$form = $this->default_form('new_' . $this->type);
		$object = $this->get_object();
		$this->add_nonce($form, array('action'=>'quick', 'company_id'=>$_SESSION['FT']['company_id']));
		$object->formbuilder_quick_insert($form);
		return $form;
	}
	
	private function action_form_new(){
		$object = $this->get_object();
		return $this->build_form_new($object);
	}
	
	private function build_form_new(toggle &$object){
		$form = $this->default_form('new_' . $this->type);
		$this->add_nonce($form, array('action'=>'insert', 'company_id'=>$_SESSION['FT']['company_id']));
		$object->formbuilder_new($form);
		return $form;
	}
	
	private function action_form_edit($id){
		$object = $this->get_object($id);
		return $this->build_form_edit($object);
	}
	
	private function build_form_edit(toggle &$object){
		$form = $this->default_form('edit_' . $this->type);
		$this->add_nonce($form, array('action'=>'update', 'id'=>$object->get_id(), 'company_id'=>$_SESSION['FT']['company_id']));
		$object->formbuilder_edit($form);
		return $form;
	}
	
	private function insert(){
		$this->test_nonce();
		$object = $this->get_object();
		if(!$object->validate_new($this->request->get_data())){
			set_postback_msg('Invalid Data - please review your entries below');
			return $this->build_form_new($object);
		}
		if(!$object->insert_new()){
			throw new toggleControllerException('Unable to save your new ' . $this->type);
		}
		return $this->build_view();
	}
	
	private function update($id){
		$this->test_nonce();
		$object = $this->get_object($id);
		if(!$object->validate_edit($this->request->get_data())){
			set_postback_msg('Invalid Data - please review your entries below');
			return $this->build_form_edit($object);
		}
		if(!$object->update_one()){
			throw new toggleControllerException('Unable to save your updates');
		}
		return $this->build_view();
	}
	
	private function toggle($id){
		$object = $this->get_object($id);
		$object->toggle_me();
		if(!$object->update_one()){
			throw new toggleControllerException('Unable to save your updates');
		}
		return $this->build_view();
	}	
}