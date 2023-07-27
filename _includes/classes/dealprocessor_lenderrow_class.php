<?php
class dealprocessor_lenderrow extends dealprocessor_row{
	protected function get_child_list(){
		return array('lender_id', 'lenderquickadd');
	}
}