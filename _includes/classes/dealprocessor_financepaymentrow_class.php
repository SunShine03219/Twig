<?php
class dealprocessor_financepaymentrow extends dealprocessor_row{
	protected function get_child_list(){
		return array('finance_payment');
	}
}