<?php
class dealprocessor_salespersonrow extends dealprocessor_row{
	protected function get_child_list(){
		return array('salesperson', 'salespersonquickadd');
	}
}