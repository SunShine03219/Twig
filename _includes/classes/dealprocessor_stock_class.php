<?php

class dealprocessor_stock extends dealprocessor_textfield{
	
	protected function get_label(){
		return 'Stock No';
	}
	
	protected function default_setting(){
		$setting = parent::default_setting();
		$setting->required = true;
		return $setting;
	}
	
	public function validate(request &$request){
		$prestock = $this->deal->get_stock();
		
		$result = parent::validate($request);
		
		if($result){
			//see if the stock number changed
			if($prestock != $this->deal->get_stock()){
				
				$newstock = $this->deal->get_stock();
				$stock_exists = $this->deal->stock_exists_in_db($newstock);
				
				if($stock_exists){
					$request->add_error($this->name, $this->get_label() . ' already exists.');
					$result = false;
				}
			}
		}
		return $result;
	}
}