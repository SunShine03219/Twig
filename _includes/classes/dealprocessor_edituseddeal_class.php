<?php
class dealprocessor_edituseddeal extends dealprocessor_form{

	protected function get_child_list(){
		return array('custvehsection', 'flooringsection', 'leadsection', 'financingsection', 'warrgapsection','pickuppayments', 'notesection', 'modaldialog');
	}
	
	protected function get_security_data(){
		return array(
				'action'=>'edit',
				'company_id' => $this->deal->get_company_id(),
				'id' => $this->deal->get_id(),
				'version' => $this->deal->get_version()
				);
	}
	
	protected function get_page_title(){
		return 'Edit ' . $this->deal->get_client_name() . '\'s deal';
	}
	
}