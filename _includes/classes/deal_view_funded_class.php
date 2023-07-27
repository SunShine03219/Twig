<?php
class deal_view_funded{
	
	public function invoke(){
	
		$caption = 'Funded Deals';
		$type = 'funded';
		$controller = new dealviewcontroller();
		return $controller->invoke($type, $caption);
	}
	
}