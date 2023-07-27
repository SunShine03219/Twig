<?php
class dealprocessor_financetradeverifiedrow extends dealprocessor_row{
	protected function get_child_list(){
		return array('finance_payoff_amount', 'finance_amount_verified_by');
	}
}