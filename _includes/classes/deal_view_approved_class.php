<?php
class deal_view_approved{
	
	public function invoke(){
		$caption = 'Approved Deals';
		$type = 'approved';
		$controller = new dealviewcontroller();
		return $controller->invoke($type, $caption);
	}
	
}