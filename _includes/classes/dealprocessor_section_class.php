<?php

abstract class dealprocessor_section extends dealprocessor_branch{
	
	abstract protected function get_caption(); 
	
	public function build_form(formbuilder &$form, $mode=''){
		if($this->setting->setting != dealsetting::SKIP){
			$this->build_section($form);
			$this->build_children($form, $mode);
		}
	}
	
	public function build_section(formbuilder &$form){
		$form->start_section('form_row');
		$form->add_subtitle($this->get_caption());
		$form->end_section();
	}
	
}