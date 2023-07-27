<?php
class dealprocessor_frontextrarow extends dealprocessor_row{
	protected function get_child_list(){
		return array('pack', 'doc_fee');
	}
} 