<?php
class deal_view_unfunded {
	
	public function invoke(){
		$caption = 'Unfunded Deals';
		$type = 'unfunded';
		$controller = new dealviewcontroller();
		return $controller->invoke($type, $caption);
	}
	
}