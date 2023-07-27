<?php
class dealprocessor_financemilesrow extends dealprocessor_row{
	protected function get_child_list(){
		return array('finance_miles');
	}
}