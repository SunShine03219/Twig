<?php
class deal_view_floored {
	
	public function invoke(){
		$caption = 'Floored Deals';
		$type = 'floored';
		$controller = new dealviewcontroller();
		return $controller->invoke($type, $caption);
	}
	
}