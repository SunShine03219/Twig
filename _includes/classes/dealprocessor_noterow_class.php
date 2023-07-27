<?php
class dealprocessor_noterow extends dealprocessor_row{
	protected function get_child_list(){
		return array('note');
	}
}