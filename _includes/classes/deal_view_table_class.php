<?php
class deal_view_table {
	
	public function invoke(){
		$caption = 'View Deals';
		$type = 'view';
		$controller = new dealviewcontroller();
		return $controller->invoke($type, $caption);
	}
}