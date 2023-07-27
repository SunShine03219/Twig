<?php
class dealprocessor_saleslistrow extends dealprocessor_row{
	protected function get_child_list(){
		return array('saleslist');
	}
}