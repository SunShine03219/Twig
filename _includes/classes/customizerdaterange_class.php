<?php

class customizerdaterange implements icustomizer{
	
	private $active;
	private $mode;
	
	private $custom_start;
	private $custom_end;
	
	private $start;
	private $end;
	
	const MODE_DATERANGE = 'daterange';
	const DATE_START = 'datestart';
	const DATE_END = 'dateend';
	const DATE_SOLD_COLUMN = 'date_sold';
	
	public function __construct(){
		$this->active = false;
		$this->mode = '';
		
		$this->customstart = first_of_month();
		$this->customend = last_of_month();
	}
	
	public function handle(&$array){
		switch($array['mode']){
			case self::MODE_DATERANGE:
				$this->handlecontroller($array['mode'], $array);
				$this->active = true;
				$this->mode = $array['mode'];
		}
	}
	
	private function handlecontroller($mode, $array){
		switch($mode){
			case self::MODE_DATERANGE:
				$this->handle_daterange($array[self::DATE_START], $array[self::DATE_END]);
				break;
		}
	}
	
	private function handle_daterange($start, $end){
		$this->custom_start = $start;
		$this->custom_end = $end;
		
		$this->start = $start;
		$this->end = $end;
	}
	
	public function build(formbuilder &$form){
		$form->start_section('form_row');
		$this->build_daterange($form);
		$form->end_section();
	}
	
	private function build_daterange(formbuilder &$form){
		$this->build_mode($form);
		$this->build_start($form);
		$this->build_end($form);
	}
	
	private function build_mode(formbuilder &$form){
		$form->add_radio('mode', self::MODE_DATERANGE, 'Custom Range', ($this->mode==self::MODE_DATERANGE));
	}
	
	private function build_start(formbuilder &$form){
		$form->add_datepicker(self::DATE_START, ($this->active?$this->custom_start:''), 'Start');
	}
	
	private function build_end(formbuilder &$form){
		$form->add_datepicker(self::DATE_END, ($this->active?$this->custom_end:''), 'End');
	}
	
	public function wheres(reportwherebuilder &$wherebuilder){
		if($this->active){
			$wherebuilder->add_where_lessequal(self::DATE_SOLD_COLUMN, $this->end);
			$wherebuilder->add_where_greatequal(self::DATE_SOLD_COLUMN, $this->start);
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
			case self::MODE_DATERANGE:
				return $this->build_headline_daterange($type);
				break;
			default:
				return 'No Title';
		}
	}
	
	private function build_headline_daterange($type){
		return sprintf('%s Report - %s to %s', $type, display_custom_date($this->custom_start), display_custom_date($this->custom_end));
	}
	
	public function get_active_vars(){
		if($this->active){
				return array('mode'=>$this->mode,
						self::DATE_START=>$this->start,
						self::DATE_END=>$this->end);
		}
	}
}