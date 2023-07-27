<?php
interface icustomizer{
	
	public function handle(&$array);
	
	public function build(formbuilder &$form);
	
	public function wheres(reportwherebuilder &$wherebuilder);
	
	public function headline($type);
	
	public function get_active_vars();
}