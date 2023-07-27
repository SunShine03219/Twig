<?php
class deal_view_delete {
	
	public function invoke(){
		$caption = 'Deals Available to Delete';
		$type = 'delete';
		$controller = new dealviewcontroller();
		return $controller->invoke($type, $caption);
	}

}