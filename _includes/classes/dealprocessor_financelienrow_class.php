<?php
class dealprocessor_financelienrow extends dealprocessor_row{
	protected function get_child_list(){
		return array('finance_lien_info');
	}
}