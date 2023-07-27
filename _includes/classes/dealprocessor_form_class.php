<?php
abstract class dealprocessor_form extends dealprocessor_branch{
	
	abstract protected function get_security_data();
	
	abstract protected function get_page_title();
	
	protected function get_notes(){
		return dealnote::build_notes($this->deal->get_id());
	}
	
	protected function get_timestamp(){
		return $this->deal->build_timestamp_data();
	}
	
	
	
	public function get_form(){
		$form = new formbuilder();
		$form->setup('deal', 'deals.php', 'POST');
		$security = $this->get_security_data();
		$this->add_nonce($security, $form);
                
		return $form;
	}
	
	private function add_nonce($data, formbuilder &$form){
		$nonce = new nonce();
		$nonce->add_array($data);
		$nonce->save();
		$form->add_hidden_array($nonce->get_nonce_list());
	}
	
	public function build_form(formbuilder &$form, $mode = ''){
		parent::build_form($form);
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
	}
}