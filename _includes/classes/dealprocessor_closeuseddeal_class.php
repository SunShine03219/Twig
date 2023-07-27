<?php
class dealprocessor_closeuseddeal extends dealprocessor_form{
	protected function get_child_list(){
		return array('custvehsection', 'flooringsection', 'financingsection', 'warrgapsection', 'closedealsection','pickuppayments', 'notesection');
	}
	
	protected function get_security_data(){
		return array(
				'action'=>'close',
				'company_id' => $this->deal->get_company_id(),
				'id' => $this->deal->get_id(),
				'version' => $this->deal->get_version()
		);
	}
	
	public function validate(request &$request){
		$initial_closed_status = $this->deal->get_closed_dms();
		if($initial_closed_status == false){
			$result = $this->validate_children($request);
		} else {
			//shortcut so that only the closeddealsection and other editable sections are tested if the deal was originally closed
			$result = $this->validate_children_array($request, array('closedealsection','pickuppayments','notesection'));
		}
		
		if(!$result){
			$this->deal->set_closed_dms($initial_closed_status); //reset the DMS closed status on validation error
		}
		return $result;
	}
	
	public function build_form(formbuilder &$form, $mode = ''){
		if($this->deal->get_closed_dms()){
			//it is closed, set modes to readonly where necessary
			$modearray = array('custvehsection'=>'readonly', 'flooringsection'=>'readonly', 'financingsection'=>'readonly', 'warrgapsection'=>'readonly');
			$this->build_form_special_modes($form, $modearray);
			$form->add_submit();

			$form->add_title($this->get_page_title());
			
			$post_buildable = new basicbuildable();
			
			$timestamp = $this->get_timestamp();
			if(!empty($timestamp)){
				$post_buildable->add_content($timestamp, 'timestamp');
				$post_buildable->add_ext_css('styles/timestamp.css');
			}
			
			$notes = $this->get_notes();
			if(!empty($notes)){
				$post_buildable->add_content($notes, 'notes_container');
			}
			
			$form->add_after($post_buildable);
			
		} else {
			parent::build_form($form, $mode);
		}
	}
	
	protected function get_page_title(){
		return 'Close ' . $this->deal->get_client_name() . '\'s deal';
	}
	
}