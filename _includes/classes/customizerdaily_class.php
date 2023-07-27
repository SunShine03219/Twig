<?php

class customizerdaily implements icustomizer{
	
	private $active;
	private $mode;
	
	private $custom_date;
	
	private $selected_date;
		
	const MODE_TODAY = 'today';
	const MODE_YESTERDAY = 'yesterday';
	const CUSTOM_DATE = 'customdate';
	const DATE_SOLD_COLUMN = 'date_sold';
	
	public function __construct(){
		$this->active = false;
		$this->mode ='';
		
		$this->custom_date = just_today();
	}
	
	public function handle(&$array){
		switch($array['mode']){
			case self::MODE_TODAY:
			case self::MODE_YESTERDAY:
				$this->handlecontroller($array['mode'], $array);
				$this->active = true;
				$this->mode = $array['mode'];
		}
	}
	
	private function handlecontroller($mode, $array){
		switch($mode){
			case self::MODE_TODAY:
				$this->handle_today();
				break;
			case self::MODE_YESTERDAY:
				$this->handle_yesterday();
				break;
		}
	}
	
	private function handle_today(){
		$this->selected_date = just_today();
	}
	
	private function handle_yesterday(){
		$this->selected_date = just_yesterday();
	}
	
	public function build(formbuilder &$form){
		$form->start_section('form_row');
		$this->build_today($form);
		$this->build_yesterday($form);
		$form->end_section();
	}
	
	private function build_today(formbuilder &$form){
		$form->add_radio('mode', self::MODE_TODAY, 'Today', ($this->mode == self::MODE_TODAY));
	}
	
	private function build_yesterday(formbuilder &$form){
		$form->add_radio('mode', self::MODE_YESTERDAY, 'Yesterday', ($this->mode == self::MODE_YESTERDAY));
	}
	
	public function wheres(reportwherebuilder &$wherebuilder){
		if($this->active){
			$wherebuilder->add_where_equal(self::DATE_SOLD_COLUMN, $this->selected_date);
		}
	}
	
	public function headline($type){
		if($this->active){
			return $this->headlinecontroller($type);
		} else {
			return false;
		}
	}
	
	private function headlinecontroller($type){
		switch($this->mode){
			case self::MODE_TODAY:
				return $this->build_headline_today($type);
				break;
			case self::MODE_YESTERDAY:
				return $this->build_headline_yesterday($type);
				break;
			default:
				return 'No Title';
		}
	}
	
	private function build_headline_today($type){
		return sprintf('%s Report - %s', $type, display_today_date());
	}
	
	private function build_headline_yesterday($type){
		return sprintf('%s Report - %s', $type, display_yesterday_date());
	}
	
	public function get_active_vars(){
		if($this->active){
			return array('mode'=>$this->mode);
		}
	}
}