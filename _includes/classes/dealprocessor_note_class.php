<?php
class dealprocessor_note extends dealprocessor_textareafield{
	
	protected function get_label(){
		return '';
	}
	
	protected function display(formbuilder &$form, $mode=''){
		$form->add_ext_css('styles/dealnotes.css');
		//only show on forms that can be edited
		if($mode != 'readonly'){
			$note = $this->deal->get_note();
			$note->formbuilder_insert($form, $mode);
		}
	}
	
	public function validate(request &$request){
		$fieldname = $this->get_name();
		$field = $request->$fieldname;
		if(!empty($field)){
			$note = $this->deal->get_note();
			$note->set_note($field); //set note performs htmlentities() for us;
		}
		return true;
	}
	
	public function set_deal_default(){
		
	}
	
}