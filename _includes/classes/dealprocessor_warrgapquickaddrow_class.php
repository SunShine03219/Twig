<?php
class dealprocessor_warrgapquickaddrow extends dealprocessor_row{
	protected function get_child_list(){
		return array('warrantyquickadd', 'gapquickadd');
	}
}