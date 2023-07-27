<?php
class dealprocessor_financestatusrow extends dealprocessor_row{
	protected function get_child_list(){
		return array('financed_deal', 'funded','approved');
	}
}