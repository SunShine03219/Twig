<?php
class dealprocessor_financingsection extends dealprocessor_section{
	
	protected function get_caption(){
		return 'Financing Information';
	}
	
	protected function get_child_list(){
		return array('financestatusrow', 'financepersonrow', 'lenderrow', 'financeamountsrow' , 'financedatesrow','financepaymentrow', 
					 'financetraderow','financetradeyearrow', 'financetradeverifiedrow','financelienrow','financeacvrow','financemilesrow','financevehiclenotesrow');
	}
	
}