<?php

class dealprocessor_factory{
	
	private $settinglist;
	
	public function __construct(dealsettinglist &$settinglist = null){
		if(!is_null($settinglist)){
			$this->set_settinglist($settinglist);
		}
	}
	
	public function make($name, deal &$deal){
		if(is_null($this->settinglist)){throw new exception ('Settinglist not set in dealprocessor factory');}

		$objectname = 'dealprocessor_' . $name;
		return new $objectname($this, $deal, $this->settinglist->$name); 
	}
	
	public function set_settinglist(dealsettinglist &$settinglist){
		$this->settinglist = $settinglist;
	}
	
}