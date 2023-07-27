<?php
class dealprocessor_warrantyrow extends dealprocessor_row{
	protected function get_child_list(){
		return array('warranty_id', 'warranty_sale', 'warranty_cost', 'warranty_gross');
	}
	
	public function validate(request &$request){
		$result = parent::validate($request);
		
		// if($result == true){
			if($this->deal->get_warranty_id() == 0 && ($this->deal->get_warranty_cost() > 0 || $this->deal->get_warranty_sale() > 0))
			{
				$result = false;
				$request->add_error('warranty_id', 'Please select a warranty provider');
			}
		//}
		
		return $result;
	
	}
}