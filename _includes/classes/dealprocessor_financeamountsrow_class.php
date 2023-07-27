<?php
class dealprocessor_financeamountsrow extends dealprocessor_row{
	protected function get_child_list(){
		return array('amount_financed', 'discount', 'dealer_check');
	}

	public function validate(request &$request){
		$result = parent::validate($request);

		// if($this->deal->get_financed_deal() == 0 && $this->deal->get_amount_financed() > 0)
		// {
		// 	$result = false;
		// 	$request->add_error('amount_financed', 'Check the Financed box');
		// }
	
		return $result;
	
	}
}