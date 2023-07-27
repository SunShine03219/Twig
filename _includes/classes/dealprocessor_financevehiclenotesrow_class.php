<?php
class dealprocessor_financevehiclenotesrow extends dealprocessor_row{
	protected function get_child_list(){
		return array('finance_vehicle_notes');
	}
}