<?php
class dealprocessor_financepersonrow extends dealprocessor_row{
	protected function get_child_list(){
		return array('finance_person', 'finance_personquickadd');
	}
}