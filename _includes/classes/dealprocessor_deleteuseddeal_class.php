<?php 

class dealprocessor_deleteuseddeal extends dealprocessor_form{

	protected function get_child_list(){
		return array('deletesection');
	}
	
	protected function get_security_data(){
		return array(
				'action'=>'delete',
				'company_id' => $this->deal->get_company_id(),
				'id' => $this->deal->get_id(),
				'version' => $this->deal->get_version()
		);
	}
	
	protected function get_page_title(){
		return 'Delete ' . $this->deal->get_client_name() . '\'s deal';
	}
}