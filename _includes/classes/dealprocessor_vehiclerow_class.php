<?php
class dealprocessor_vehiclerow extends dealprocessor_row{
	protected function get_child_list(){
		return array('year', 'make', 'model');
	}
}