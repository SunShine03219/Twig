<?php
class dealprocessor_leadrow extends dealprocessor_row{
	protected function get_child_list(){
		return array('lead_id', 'leadquickadd');
		//'flooring_paid', 'flooring_date'
	}
}