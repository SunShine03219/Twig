<?php
class dealprocessor_clientrow extends dealprocessor_row{
	protected function get_child_list(){
		return array('client_name', 'client_phone', 'stock');
	}
}