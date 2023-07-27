<?php

abstract class dealprocessor_row extends dealprocessor_branch{
	
	public function build_form(formbuilder &$form, $mode = ''){
		if($this->setting->setting != dealsetting::SKIP){
			$form->start_section('form_row');
			$this->build_children($form, $mode);
			$form->end_section();
		}
	}
	
}