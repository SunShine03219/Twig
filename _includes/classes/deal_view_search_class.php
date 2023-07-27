<?php
class deal_view_search {
	
	public function invoke(){
		$caption = 'Search Deals';
		$type = 'search';
		$controller = new dealviewcontroller();
		$output = $controller->invoke($type, $caption);
		//$output->add_before($this->build_search_form());
		return $output;
	}
	
	private function build_search_form(){
		$search = '';
		if(isset($_GET['search'])){
			$search = urldecode($_GET['search']);
		}
		
		$form = $this->get_form();
		$form->start_section('form_row');
		$form->add_subtitle('Search Deals');
		$form->end_section();
		$form->start_section('form_row');
		$form->add_text('search', 'Search', $search);
		$form->end_section();
		$form->add_submit();
		return $form;
	}
	
	private function get_form(){
		$form = new formbuilder();
		$form->setup('search', $_SERVER['SCRIPT_NAME'], 'get');
		$form->add_hidden('action', 'search');
		//$this->add_nonce($form, array('action'=>'search'));
		return $form;
	}
	
	protected function add_nonce(formbuilder &$form, $array){
		$nonce = new nonce();
		$nonce->add_array($array);
		$nonce->save();
		$form->add_hidden_array($nonce->get_nonce_list());
	}
}