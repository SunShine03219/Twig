<?php
class dealprocessor_financeacvrow extends dealprocessor_row{
	protected function get_child_list(){
		return array('finance_acv');
	}
}