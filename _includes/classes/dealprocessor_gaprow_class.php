<?php
class dealprocessor_gaprow extends dealprocessor_row{
	protected function get_child_list(){
		return array('gap_id', 'gap_sale', 'gap_cost', 'gap_gross');
	}
	
	public function validate(request &$request){
		$result = parent::validate($request);
	
		//if($result == true){
			if($this->deal->get_gap_id() == 0 && ($this->deal->get_gap_cost() > 0 || $this->deal->get_gap_sale() > 0))
			{
				$result = false;
				$request->add_error('gap_id', 'Please select a gap provider');
			}
		//}
	
		return $result;
	
	}
}