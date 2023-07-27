<?php
class nonce{
	
	private $data;
	private $key;
	
	private $quickandpainlessdeath;
	
	private function set_key($key){
		$key = trim($key);
		$this->key = $key;
	}
	
	public function __construct($key = null){
		$this->quickandpainlessdeath = false;
		$this->data = array();
		
		if(empty($key)){
			$this->set_key($this->get_new_key());
		} else {
			$this->set_key($key);
			$this->read();
		}
	}
	
	private function get_new_key(){
		return substr(sha1(rand()), 0, 16);
	}
	
	private function read(){
		if(empty($this->key)){
			$this->quickandpainlessdeath = true;
			return false;
		}
		$ftKey = isset($_SESSION['FT'][$this->key]) ? $_SESSION['FT'][$this->key] : null;
		$this->data = $ftKey;
		if($ftKey != false)
			unset($_SESSION['FT'][$this->key]);
	}
	
	public function get_data(){
		return $this->data;
	}
	
	public function get_nonce_list(){
		$data = $this->get_data();
		$data['nonce'] = $this->key;
		return $data;
	}
	
	public function save(){
		$_SESSION['FT'][$this->key] = $this->data;
	} 
	
	public function test($key, $value){
		if($this->quickandpainlessdeath) return false;
		return $this->data[$key] == $value;
	}
	
	public function add($key, $value){
		$this->data[$key] = $value; 
	}
	
	public function add_array($array){
		foreach($array as $key=>$value){
			$this->add($key, $value);
		}
	}
	
	public function test_request($request){
		if($this->quickandpainlessdeath) return false;
		if(!is_array($this->data)) return false;
		$result = true;
		foreach(array_keys($this->data) as $key){
			if($key != 'session'){
				$result = ($result && $this->test($key, $request[$key]));
			}
		}
		return $result;
	}
	
}