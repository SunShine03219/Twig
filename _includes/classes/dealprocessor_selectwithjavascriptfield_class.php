<?php
abstract class dealprocessor_selectwithjavascriptfield extends dealprocessor_selectfield{
	protected function display(formbuilder &$form, $mode = ''){
		$form->add_select_with_javascript($this->name, $this->get_option_list(), $this->get_javascript(), $this->get_label(), $this->get_default_id(), $this->get_default_val(), $this->setting->required, $mode=='readonly');
	}
	
	abstract protected function get_javascript();
}