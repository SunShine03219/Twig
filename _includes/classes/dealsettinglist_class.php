<?php

class dealsettinglist{
	
	private $list;
	
	const TABLE = 'dealsettings';
	
	public function __construct(){
		$this->list = array();
	}
	
	public function load($array){
		foreach($array as $setting){
			$object = new dealsetting();
			$object->from_array($setting);
			$this->list[$object->name] = $object;
		}
	}
	
	public function __get($name){
		return $this->get_setting($name);
	}
	
	public function get_setting($name){
		return $this->list[$name];
	}
	
	public function remove_setting($name){
		unset($this->list[$name]);
	}
	
	public function retrieve_setting($name){
		if(isset($this->list[$name])){
			return $this->get_setting($name);
		}
		$setting = new dealsetting();
		$setting->name = $name;
		return $setting;
	}
	
	public function get_setting_count(){
		return count($this->list);
	}
	
	public function add_setting(dealsetting &$setting){
		$this->list[$setting->name] = $setting;
	}
	
	private function validate_company_id($company_id){
		$company_id = intval($company_id);
		if($company_id > 0){
			return $company_id;
		} else {
			return 0;
		}
	}
	
	public function load_company_id($company_id){
		if($cid = $this->validate_company_id($company_id)){
			return $this->load_list_from_db($cid);
		} else {
			throw new Exception('Invalid Company ID sent to Load. ' . $company_id);
		}
	}
	
	private function load_list_from_db($company_id){
		
		if(db_exists(self::TABLE, 'company_id', $company_id)){
			return $this->retrieve_list_from_db($company_id);
		}
		return true; //there are no settings to load
	}
	
	private function retrieve_list_from_db($company_id){
		$wheres = array('company_id'=>$company_id);
		$columns = dealsetting::get_db_columnlist();
		$result = db_select(self::TABLE, $wheres, '', $columns);
		$this->load($result);
		return true;
	}
	
	public function save_company_id($company_id){
		if($cid = $this->validate_company_id($company_id)){
			return $this->prepare_save($cid);
		} else {
			throw new Exception('Invalid Company ID sent to Save. ' . $company_id);
		}
	}
	
	private function prepare_save($company_id){
		$this->delete_company_id($company_id);
		return $this->save_list_to_db($company_id);
	}
	
	private function save_list_to_db($company_id){
		$data = $this->to_array();
		if(!is_array($data) || empty($data)){
			return true; //no settings to save
		}
		for($i = 0; $i < count($data); $i++){
			$data[$i]['company_id'] = $company_id;
		}
		return db_insert_assoc(self::TABLE, $data);
	}
	
	private function delete_company_id($company_id){
		$whereclause = db_build_where(array('company_id'=>$company_id));
		$sql = 'DELETE from ' . self::TABLE . ' ' . $whereclause;
		db_delete_bare($sql);
	}
	
	public function to_array(){
		$array = array();
		foreach($this->list as $name=>$setting){
			$array[] = $setting->to_array();
		}
		return $array;
	}
	
	public function debug_display_settings(){
		foreach($this->list as $name=>$setting){
			echo "$name - <br/>\n";
			foreach($setting->to_array() as $k=>$v){
				echo "\t$k - $v<br/>\n";
			}
		}
		echo 'Count - ' . $this->get_setting_count() . "<br/>\n";
	}
	
}