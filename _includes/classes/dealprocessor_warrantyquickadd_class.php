<?php
class dealprocessor_warrantyquickadd extends dealprocessor_leaf{
	
	public function set_deal_default(){	}
	
	protected function get_label() {}
	
	public function calculate(){}
	
	public function validate(request &$request){
		return true;
	}
	
	protected function display(formbuilder &$form, $mode = ''){
		if($_SESSION['FT']['SEC_ADMIN']){
			$form->add_custom('<span class="modalbtn" onclick="showmodal(\'warranty\');">New Warranty Provider</span>');
		}
	}
	
	
	
} 