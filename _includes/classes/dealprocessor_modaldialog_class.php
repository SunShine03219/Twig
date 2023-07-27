<?php
class dealprocessor_modaldialog extends dealprocessor_leaf{
	
	protected function display(formbuilder &$form, $mode = ''){
		$output = new basicbuildable();
		
		$output->add_ext_js('//code.jquery.com/ui/1.10.4/jquery-ui.min.js');
		$output->add_ext_js('scripts/togglemodal.js');
		$output->add_ext_css('//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css');
		$output->add_ext_css('styles/togglemodal.css');
		$output->add_int_js('$(function() {$( "#dialog-modal" ).dialog({height: 400, width:800, modal: true, autoOpen: false});
});');
		
		$output->add_content('', '', 'dialog-modal', ' title=\'Add New\'');
		
		$form->add_after($output);
	}
	
	public function calculate(){}
	
	public function validate(request &$request){
		return true;
	}
	
	public function set_deal_default(){}
	
	public function get_label(){}
	
}