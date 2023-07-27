<?php

class dealprocessor_newuseddeal extends dealprocessor_form{
	
	protected function get_child_list(){
		return array('custvehsection', 'flooringsection', 'leadsection', 'financingsection', 'warrgapsection','pickuppayments', 'notesection', 'modaldialog');
	}
	
	protected function get_security_data(){
		return array(
				'action'=>'new',
				'company_id' => $this->deal->get_company_id(),
				'id' => $this->deal->get_id(),
				'version' => $this->deal->get_version()
		);
	}
	
	protected function get_page_title(){
		return 'Enter new deal';
	}
	
	protected function get_timestamp(){
		
	}
	
	
	protected function get_notes(){
		
	}
}