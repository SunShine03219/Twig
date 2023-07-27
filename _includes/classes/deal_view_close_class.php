<?php
class deal_view_close {
	
	public function invoke(){
		$caption = 'Deals Ready to Close';
		$type = 'close';
		$controller = new dealviewcontroller();
		return $controller->invoke($type, $caption);
	}
}