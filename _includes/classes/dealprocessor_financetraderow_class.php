<?php
class dealprocessor_financetraderow extends dealprocessor_row{
	protected function get_child_list(){
		return array('finance_trade', 'finance_payoff');
	}
}