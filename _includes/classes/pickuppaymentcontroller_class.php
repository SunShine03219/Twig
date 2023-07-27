<?php
class pickuppaymentControllerException extends Exception{
	public function __construct($message, $code=0, Exception $previous=null){
		parent::__construct($message, $code, $previous);
	}
	public function __toString() {
		return $this->message;
	}
}

class pickuppaymentcontroller extends controller{
	
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
		}
		catch (paymentmethodControllerException $e){
			return $this->build_error((string)$e);
		}
		throw new controllerException('Fell out of execute');
	}
	
	private function get_object($id = null){
		$object = new pickuppayment();
		if($id > 0){
			if(!$object->make_me_into($id)){
				throw new pickuppaymentControllerException('Unable to retrieve your ' . $this->type . ' ' . $id);
			}
		}
		return $object;
	}
	
	private function handle_get(){
		$action = trim(urldecode($this->request->action));
		$id = intval(urldecode($this->request->id));
		$deal_id = intval(urldecode($this->request->deal_id));		

		if(empty($action) && empty($id) && empty($deal_id) || $action=='view'){
		    return $this->build_view();
		}
		if( ($action == 'pay' || $action == 'unpay' || $action == 'delete' || $action == 'undelete') && !empty($id)){
			return $this->handle_action($id, $action);
		}
		return $this->build_error('Invalid Request');
	}
	
	private function build_view(){
		$view = new pickuppaymentview();
		$output = $view->invoke();
		return $output;
	}
	
	private function handle_post(){
		$action = trim(urldecode($this->request->action));
		return $this->build_error('Invalid Request');
	}
	
	private function handle_action($id, $action){
		$object = $this->get_object($id);
		return $this->run_action($object, $action);
	}
	
	private function run_action(pickuppayment $object, $action){
		switch($action){
			case 'pay':
				$object->add_action('paid', 'paid');
				break;
			case 'unpay':
				$object->add_action('paid', 'unpaid');
				break;
			case 'delete':
				$object->add_action('delete', 'delete');
				break;
			case 'undelete':
				$object->add_action('delete', 'undelete');
				break;
			default:
				throw new pickuppaymentControllerException('Unable to run ' . $action);
		}
		$object->commit($object->get_deal_id());
		return $this->build_view();
	}
	
	
}