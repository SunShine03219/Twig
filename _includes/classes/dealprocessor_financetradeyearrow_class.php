<?php
class dealprocessor_financetradeyearrow extends dealprocessor_row{
	protected function get_child_list(){
		return array('finance_year', 'finance_makemodel');
	}
}