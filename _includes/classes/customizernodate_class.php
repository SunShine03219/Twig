<?php
class customizernodate implements icustomizer{
	
	private $active;
	private $mode;
	
	const MODE_UNLIMITED = 'unlimited';
	
	public function __construct(){
		$this->active = false;
		$this->mode = '';
	}
	
	public function handle(&$array){
		switch($array['mode']){
			case self::MODE_UNLIMITED:
				$this->handlecontroller($array['mode'], $array);
				$this->active = true;
				$this->mode = $array['mode'];
		}
	}
	
	private function handlecontroller($mode, $array){
		switch($mode){
			case self::MODE_UNLIMITED:
				$this->handle_unlimited();
				break;
		}
	}
	
	private function handle_unlimited(){
		//nothing to do but twittle our fingers
	}
	
	public function build(formbuilder &$form){
		$form->start_section('form_row');
		$this->build_unlimited($form);
		$form->end_section();
	}
	
	private function build_unlimited(formbuilder &$form){
		$form->add_radio('mode', self::MODE_UNLIMITED, 'All Time', ($this->mode == self::MODE_UNLIMITED));
	}
	
	public function wheres(reportwherebuilder &$wherebuilder){
		if($this->active){
			//nothing to do
		}
	}
	
	public function headline($type){
		if($this->active){
			return sprintf('%s Report - All Time', $type);
		} else {
			return false;
		}
	}
	
	public function get_active_vars(){
		if($this->active){
			return array('mode'=>$this->mode);
		}
	}
}