<?php

class controllerException extends Exception{
	public function __construct($message, $code = 0, Exception $previous = null){
		parent::__construct($message, $code, $previous);
	}
	
	public function __toString() {
		return $this->message;
	}
}

class nonceException extends controllerException{
	public function __construct($message = 'Invalid Security Data.', $code=0, Exception $previous = null){}
}

abstract class controller{
	
	protected $request;
	
	public function __construct(){
		$this->request = new genericrequest();
	}
	
	final public function invoke(){
		try {
			return $this->execute();
		} catch (controllerException $e) {
			return $this->build_error((string)$e);
		}
	}
	
	abstract protected function execute();
	
	protected function default_form($name){
		$form = new formbuilder();
		$form->setup($name, $_SERVER['SCRIPT_NAME'], 'POST');
		return $form;
	}
		
	protected function add_nonce(formbuilder &$form, $array){
		$nonce = new nonce();
		$nonce->add_array($array);
		$nonce->save();
		$form->add_hidden_array($nonce->get_nonce_list());
	}
	
	protected function test_nonce(){
		$nonce = new nonce($this->request->nonce);
		if(!($nonce->test_request($this->request->get_data()))){
			throw new nonceException();
		}
	}
	
	protected function build_error($msg){
		$object = new basicbuildable();
		$object->add_content($msg, 'error');
		return $object;
	}
	
}