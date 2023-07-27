<?php
class deal_view_pending {
	
	public function invoke(){
		$caption = 'Pending Documents';
		$type = 'pending';
		$controller = new dealviewcontroller();
		return $controller->invoke($type, $caption);
	}
}