<?php
class dealprocessor_deskmanager extends dealprocessor_selectwithnothingfield{
	
	protected function get_options(){
		return deskmanager::get_active_deskmanagers($this->deal->get_company_id());
	}
	
	protected function get_label(){
		return 'Deskmanager';
	}
	
	protected function get_default_val(){
		return deskmanager::get_deskmanager_name($this->deal->get_deskmanager());
	}
}