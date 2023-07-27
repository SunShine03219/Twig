<?php
class dealprocessor_saleslist extends dealprocessor_leaf{
	
	protected function get_label(){
		return 'Salesperson List';
	}
	
	public function calculate(){
		//all important calculation fields are done in deal class on set_salespeople
	}
	
	public function validate(request &$request){
		$salespeople_list = &$request->salespeople;
		$this->deal->set_salespeople($salespeople_list);
		return true;
	}
	
	public function display(formbuilder &$form, $mode=''){
		$html = array();
		$html[] = '<div id="sales_list">';
		$sales_ids = $this->deal->get_salespeople();
		if(count($sales_ids)>0){
			$names = sales::get_salespeople_names($sales_ids);
			$count = 0;
			foreach($sales_ids as $sales_id){
				$html[] = $this->build_salesperson($count, $sales_id, $names[$sales_id], $mode);
				$count++;
			}
		}
		$html[] = "\t".'</div><!-- end sales_list -->';
		$form->add_inline_html( implode(PHP_EOL, $html) );
		$form->add_ext_js('//code.jquery.com/jquery-1.10.2.min.js');
		$form->add_ext_js('scripts/add_sales.js');
	}
	
	private function build_salesperson($ext_id, $sales_id, $name, $mode=''){
		$name = (empty($name)?'Unknown':$name);
		$html = "\t";
		$html .= '<div id="presales'.$ext_id.'" class="salesperson">';
		$html .= '<input type="hidden" value="'.$sales_id.'" name="salespeople[]">';
		$html .= '<label>' . $name . '</label>';
		if($mode != 'readonly'){
			$html .= '<img src="images/minus.png" title="Remove '.$name.'" onclick="remove_salesperson(presales' . $ext_id . ');">';
		}
		$html .= '</div>';
		return $html;
	}
	
	public function set_deal_default(){
		
	}
}