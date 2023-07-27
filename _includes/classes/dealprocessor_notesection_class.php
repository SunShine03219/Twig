<?php
class dealprocessor_notesection extends dealprocessor_section{
	
	protected function get_caption(){
		return 'Deal Note';
	}
	
	protected function get_child_list(){
		return array('noterow');
	}
}