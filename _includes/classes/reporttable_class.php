<?php

class reporttable{
	
	private $id;
	private $class;
	private $columnlist;
	private $extra_table_settings;
	
	private $tbody_class;
	private $tbody_id;
	
	private $sortid;
	private $sortdir;
	private $sortprefix;
	private $get_vars;
	private $get_vars_string;
	private $get_vars_string_dirty;
	
	private $title;
	
	private $row;
	private $widths;
	private $data;
	private $heading;
	
	const PATTERN_MATCH = '/\{([\w_]*)\}/'; //basicaly match brace enclosed as $m[1]
	const MATCH_FORMAT = '{%s}'; //sprintf format string, brace enclose
	
	public function __construct($class = null, $id = null){
		$this->id = $id;
		$this->class = $class;
		$this->get_vars = array();
		$this->get_vars_string_dirty = true;
		
		$this->tbody_class = '';
		$this->tbody_id = '';
	}
	
	public function set_id($id){
		$this->id = $id;
	}
	
	public function set_class($class){
		$this->class = $class;
	}
	
	public function set_tbody_id($id){
		$this->tbody_id = $id;
	}
	
	public function set_tbody_class( $class=''){
		$this->tbody_class = $class;		
	}
	
	public function set_extra_settings($settings){
		$this->extra_table_settings = $settings;
	}
	
	private function create_get_vars_string(){
		$list = array();
		foreach($this->get_vars as $k=>$d){
			$list[] = urlencode($k) . '=' . urlencode($d); 
		}
		$this->get_vars_string = join('&',$list);
		$this->get_vars_string_dirty = false;
	}
	
	public function add_get_var($id, $val){
		$this->get_vars[$id] = $val;
		$this->get_vars_string_dirty = true;
	}
	
	public function add_get_vars($array){
		foreach($array as $k=>$d){
			$this->add_get_var($k, $d);
		}
	}
	
	private function retrieve_get_vars_string(){
		if($this->get_vars_string_dirty){
			$this->create_get_vars_string();
		}
		return $this->get_vars_string;
	}
	
	public function set_columnlist(reportcolumnlist &$cl){
		$this->columnlist = $cl;
	}
	
	public function set_active_sort($id, $dir){
		$this->sortid = $id;
		$this->sortdir = $dir;
	}
	
	public function set_active_sort_array($array){
		$this->sortid = $array['id'];
		$this->sortdir = $array['dir'];
	}
		
	public function set_sort_prefix($prefix){
		$this->sortprefix = $prefix;
	}
	
	public function set_title($title){
		$this->title = $title;
	}
	
	public function build($data, $total='', $avg='', $proj=''){
		$object = new basicbuildable();
		$object->add_ext_css('styles/reporttable.css');
		$object->add_content($this->build_html($data, $total, $avg, $proj));		
		return $object;
	}
	
	public function build_html($data, $total='', $avg='', $proj=''){
		$output = array();
		$output[] = $this->get_title();
		$output[] = $this->start_table();
		$output[] = $this->get_widths();
		$output[] = $this->start_tbody();
		$output[] = $this->row_wrap($this->build_heading(), 'heading');
		$output[] = $this->display($data);
		if(is_array($total) && !empty($total)){
			$output[] = $this->display_one($total, 'total');
		}
		if(is_array($avg) && !empty($avg)){
			$output[] = $this->display_one($avg, 'avg');
		}
		if(is_array($proj) && !empty($proj)){
			$output[] = $this->display_one($proj, 'projected');
		}
		$output[] = $this->end_tbody();
		$output[] = $this->end_table();
		return join(PHP_EOL, $output);
	}
	
	public function build_only_heading(){
		$output = array();
		$output[] = $this->start_table();
		$output[] = $this->get_widths();
		$output[] = $this->start_tbody();
		$output[] = $this->row_wrap($this->build_heading(), 'heading');
		$output[] = $this->end_tbody();
		$output[] = $this->end_table();
		return join(PHP_EOL, $output);
	}
	
	public function build_no_heading($data, $total='', $avg='', $proj=''){
		$output = array();
		$output[] = $this->start_table();
		$output[] = $this->get_widths();
		$output[] = $this->start_tbody();
		if(is_array($data) && !empty($data)){
			$output[] = $this->display($data);
		}
		if(is_array($total) && !empty($total)){
			$output[] = $this->display_one($total, 'total');
		}
		if(is_array($avg) && !empty($avg)){
			$output[] = $this->display_one($avg, 'avg');
		}
		if(is_array($proj) && !empty($proj)){
			$output[] = $this->display_one($proj, 'projected');
		}
		$output[] = $this->end_tbody();
		$output[] = $this->end_table();
		return join(PHP_EOL, $output);
	}
	
	private function get_title(){
		return (empty($this->title)?'':$this->build_title());
	}
	
	private function build_title(){
		return '<div class="table_title_container"><span>' . $this->title . '</span><div class="rule"></div></div><div class="gap"></div>';
	}
	
	private function start_table(){
		return sprintf('<table%s%s%s cellspacing="0" cellpadding="0">',
				(empty($this->id)?'':' id="'.$this->id.'"'),
				(empty($this->class)?'':' class="'.$this->class.'"'),
				(empty($this->extra_table_settings)?'':' ' . $this->extra_table_settings)
				);
	}
	
	private function end_table(){
		return '</table>';
	}
	
	private function row_wrap($html, $class=null, $id=null){
		return sprintf('<tr%s%s>%s</tr>',
				(empty($class)?'':' class="'.$class.'"'),
				(empty($id)?'':' id="'.$id.'"'),
				$html
				);
	}
	
	private function start_tbody(){
		return '<tbody' . (empty($this->tbody_class)?'':' class="'.$this->tbody_class.'"') . (empty($this->tbody_id)?'':' id="'.$this->tbody_id.'"') . '>';
	}
	
	private function end_tbody(){
		return '</tbody>';
	}
	
	private function display(&$data, $class=null, $id=null){
		$subject = $this->get_data();
		$callback = $this->callback_nbsp($row);
		$pattern = self::PATTERN_MATCH;
		$rows = array();
		foreach($data as $row){
			$lc = (isset($row['class'])?$row['class']:$class);
			$li = (isset($row['id'])?$row['id']:$id);
			$rows[] = $this->row_wrap(preg_replace_callback($pattern, $callback, $subject), $lc, $li);
		}
		return join(PHP_EOL, $rows);
	}
	
	private function display_one(&$data, $class=null, $id=null){
		$subject = $this->get_data();
		$callback = $this->callback_nbsp($data);
		$pattern = self::PATTERN_MATCH;
		return $this->row_wrap(preg_replace_callback($pattern, $callback, $subject), $class, $id);
	}
	
	private function get_widths(){
		if(empty($this->widths)){
			$this->widths = $this->rebuild_widths();
		}
		return $this->widths;
	}
	
	private function rebuild_widths(){
		$widths = $this->columnlist->get_widths();
		if(empty($widths)){
			return '';
		}
		return '<col width="' . join('"><col width="', $widths) . '">';
	}
	
	private function get_row(){
		if(empty($this->row)){
			$this->row = $this->build_row();
		}
		return $this->row;
	}
	
	private function build_row(){
		$order = $this->columnlist->get_order();
		$classes = $this->columnlist->map_id_class();
		foreach($order as $id){
			$cols[] = sprintf('<td%s>%s</td>',
					$this->format_class($classes[$id]),
					$this->format_id($id));
		}
		return join ('', $cols);
	}
	
	private function get_data(){
		if(empty($this->data)){
			$this->data = $this->build_data();
		}
		return $this->data;
	}
	
	private function build_data(){
		return $this->get_row();
	}
	
	private function get_heading(){
		if(empty($this->heading)){
			$this->heading = $this->build_heading();
		}
		return $heading;
	}
	
	private function build_heading(){
		$subject = $this->build_row();
		$data = $this->build_combined_headings();
		$callback = $this->callback_basic($data);
		$output = preg_replace_callback(self::PATTERN_MATCH, $callback, $subject);
		return $output;
	}
	
	private function build_combined_headings(){
		$headings = $this->columnlist->map_id_caption();
		$sorts = $this->columnlist->map_id_sort();
		foreach($headings as $id=>$heading){
			$formatted[$id] = $this->format_sortableheading($heading, $sorts[$id]);
		}
		return $formatted;
	}
	
	private function format_id($id){
		return sprintf(self::MATCH_FORMAT, $id);
	}
	
	private function format_class($class){
		return empty($class)?'':' class="'.$class.'"';
	}
	
	private function format_sortableheading($heading, $sort = ''){
		if(empty($sort)){
			return $this->format_heading_nosort($heading);
		}
		if($sort == $this->sortid){
			return $this->format_heading_active($heading, $sort);
		} else {
			return $this->format_heading_sortable($heading, $sort);
		}
	}
	
	private function format_heading_nosort($heading){
		return $heading;
	}

	
	private function format_heading_sortable($heading, $sort, $dir='asc', $img=''){
		//<a href=page.php?prefixsortid=$sort&prefixsortdir=$dir&$otherget>heading</a><img />
		$othervars = htmlspecialchars($this->retrieve_get_vars_string());
		return sprintf('<a href="%s?%ssortid=%s&amp;%ssortdir=%s%s">%s</a>%s',
				$_SERVER['SCRIPT_NAME'],
				$this->sortprefix,
				$sort,
				$this->sortprefix,
				$dir,
				(empty($othervars)?'':'&amp;'.$othervars),
				$heading,
				$img
				);
	}
	
	private function format_heading_active($heading, $sort){
		$dir = 'desc';
		$img = '<img src="images/arrow_up.png" />';
		if($this->sortdir == 'desc'){
			$dir = 'asc';
			$img = '<img src="images/arrow_down.png" />';
		}
		return $this->format_heading_sortable($heading, $sort, $dir, $img);
	}
	
	private function callback_basic(&$data){
		return function ($m) use (&$data){
			return (isset($data[$m[1]])?$data[$m[1]]:$m[0]);
		};
	}
	
	private function callback_blank(&$data){
		return function ($m) use (&$data){
			return ($data[$m[1]]);
		};
	}
	
	private function callback_nbsp(&$data){
		return function ($m) use (&$data){
			return (isset($data[$m[1]])?$data[$m[1]]:'&nbsp;');
		};
	}
	
	
}