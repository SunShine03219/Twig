<?php

class dealnote{
	
	/*
	 * ------------------------------------------------------------
	 * VARIABLES
	 * ------------------------------------------------------------
	 */
	
	private $id;
	private $deal_id;
	private $user_id;
	private $posted;
	private $note;
	
	const DEALNOTE_MAX_LENGTH = 2000;
	const TABLE = 'dealnotes';
	
	

	/*
	 * ------------------------------------------------------------
	* CONSTRUCTOR
	* ------------------------------------------------------------
	*/
	
	public function __construct(){
		$this->zero_it();
	}
	
	
	/*
	 * ------------------------------------------------------------
	* ZERO IT
	* ------------------------------------------------------------
	*/
	
	public function zero_it(){
		$this->id = 0;
		$this->deal_id = 0;
		$this->user_id = 0;
		$this->posted = 0;
		$this->note = "";
	}
	
	
	/*
	 * ------------------------------------------------------------
	* POPULATE
	* ------------------------------------------------------------
	*/
	
	private function populate(){
		if(empty($this->id)) {$this->zero_it(); return false;}
		
		$where = array('id' => $this->id);
		$data = db_select_one(self::TABLE, $where);
		
		if(!$data){$this->zero_it(); return false;}
		
		$this->set_id($data['id']);
		$this->set_deal_id($data['deal_id']);
		$this->set_user_id($data['user_id']);
		$this->set_posted($data['posted']);
		$this->set_note($data['note']);
		
		return true;
	}
	
	
	/*
	 * ------------------------------------------------------------
	* MAKE_ME_INTO
	* ------------------------------------------------------------
	*/
	
	public function make_me_into($v){
		$this->set_id($v);
		$result = $this->populate();
		return $result;
	}
	
	
	/*
	 * ------------------------------------------------------------
	* ACCESSORS
	* ------------------------------------------------------------
	*/
	
	public function get_id(){
		return $this->id;
	}
	
	public function get_deal_id(){
		return $this->deal_id;
	}
	
	public function get_user_id(){
		return $this->user_id;
	}
	
	public function get_posted(){
		return $this->posted;
	}
	
	public function get_note(){
		return $this->note;
	}
	
	public function have_note(){
		return !empty($this->note);
	}
	
	

	/*
	 * ------------------------------------------------------------
	* MODIFIERS
	* ------------------------------------------------------------
	*/
	
	public function set_id($v){
		$v = intval(trim($v));
		if(is_numeric($v) && $v != 0){
			$this->id = $v;
		} else {
			$this->id = 0;
		}
	}
	
	public function set_deal_id($v){
		$v = intval(trim($v));
		if(is_numeric($v) && $v != 0){
			$this->deal_id = $v;
		} else {
			$this->deal_id = 0;
		}
	}
	
	public function set_user_id($v){
		$v = intval(trim($v));
		if(is_numeric($v) && $v != 0){
			$this->user_id = $v;
		} else {
			$this->user_id = 0;
		}
	}
	
	public function set_posted($v){
		$v = intval(trim($v));
		if(is_numeric($v) && $v != 0){
			$this->posted = $v;
		} else {
			$this->posted = 0;
		}
	}
	
	public function set_note($v){
		$v = htmlentities(trim($v));
		if($v){
			$this->note = $v;
		} else {
			$this->note = "";
		}
	}
	
	public function set_posted_now(){
		$this->set_posted(time());
	}
	
	public function set_current_user(){
		$this->set_user_id($_SESSION['FT']['user_id']);
	}
	

	/*
	 * ------------------------------------------------------------
	* TO_ARRAY
	* ------------------------------------------------------------
	*/
	
	public function to_array(){
		$a = array();
		$a['id'] = $this->get_id();
		$a['deal_id'] = $this->get_deal_id();
		$a['user_id'] = $this->get_user_id();
		$a['posted'] = $this->get_posted();
		$a['note'] = $this->get_note();
		return $a;
	}
	

	/*
	 * ------------------------------------------------------------
	* FROM_ARRAY
	* ------------------------------------------------------------
	*/
	
	public function from_array(&$a){
		$this->set_id($a['id']);
		$this->set_deal_id($a['deal_id']);
		$this->set_user_id($a['user_id']);
		$this->set_posted($a['posted']);
		$this->set_note($a['note']);
	}
	
	

	/*
	 * ------------------------------------------------------------
	* VALIDATE_NEW
	* ------------------------------------------------------------
	*/
	
	public function validate_new(&$a){
		$error_flag = false;
		
		//deal_id req'd
		if($a['deal_id'] == 0){
			$error_flag = true;
			add_postback_error('deal_id', 'Deal ID not set for note');
		} else {
			$this->set_deal_id($a['deal_id']);
		}
		
		//user_id req'd defaults to logged in user
		if($a['user_id'] == 0 ){
			$this->set_current_user();
		} else {
			$this->set_user_id($a['user_id']);
		}
		
		//posted time is set upon insert
		
		//see that the note exists and isn't too long
		$length = strlen(trim($a['note'])); 
		if($length == 0){
			$error_flag = true;
			add_postback_error('note', 'Note cannot be blank');
		} elseif ($length > self::DEALNOTE_MAX_LENGTH) {
			$error_flag = true;
			add_postback_error('note', 'Note cannot be over ' . self::DEALNOTE_MAX_LENGTH . ' characters.');
		} else {
			$this->set_note($a['note']);
		}
		
		if($error_flag){
			set_postback_msg('Invalid Data - Please review your entries below');
			return false;
		} else {
			return true;
		}
			
	}
	
	public function validate_popup(&$a){
		$error_flag = false;
		
		//deal_id req'd
		if($a['deal_id'] == 0){
			$error_flag = true;
			add_postback_error('deal_id', 'Deal ID not set for note');
		} else {
			$this->set_deal_id($a['deal_id']);
		}
		
		//user_id req'd defaults to logged in user
		if($a['user_id'] == 0 ){
			$this->set_current_user();
		} else {
			$this->set_user_id($a['user_id']);
		}
		
		//see that the note exists and isn't too long
		$length = strlen(trim($a['note']));
		if($length == 0){
			$error_flag = true;
			add_postback_error('note', 'Note cannot be blank');
		} elseif ($length > self::DEALNOTE_MAX_LENGTH) {
			$error_flag = true;
			add_postback_error('note', 'Note cannot be over ' . self::DEALNOTE_MAX_LENGTH . ' characters.');
		} else {
			$this->set_note($a['note']);
		}
		
		if($error_flag){
			set_postback_msg('Invalid Data - Please review your entries below');
			return false;
		} else {
			return true;
		}
	}
	
	
	/*
	 * ------------------------------------------------------------
	* INSERT_NEW
	* ------------------------------------------------------------
	*/
	
	public function insert_new(){
		//generate a posted time
		$this->set_posted_now();
		
		$data = $this->to_array();
		unset($data['id']);
		
		$result = db_insert_assoc_one(self::TABLE, $data);
		
		if($result){
			$this->set_id(db_get_insert_id());
		}
		return $result;
	}
	
	/*
	 * ------------------------------------------------------------
	* PREPARE NEW
	* ------------------------------------------------------------
	*/
	public function prepare_new(){
		$this->set_current_user();
	}

	/*
	 * ------------------------------------------------------------
	* Formbuilders - public
	* ------------------------------------------------------------
	*/
	//this one is going to take some thinking
	public function formbuilder_new_deal(formbuilder &$form){
		//new deals just use a note box that becomes the first note
		$form->start_section("form_row");
		$form->add_subtitle("Deal Notes");
		$form->end_section();
		$form->start_section("form_row");
			$this->formbuilder_note($form);
		$form->end_section();
	}
	
	public function formbuilder_insert(formbuilder &$form, $mode = ''){
		$this->formbuilder_note($form, $mode!='readonly');
	}
	
	public function formbuilder_edit_deal($deal_id){
		//edited deals or other note making procedures
	}
	
	public function formbuilder_popup(){
		$form = new formbuilder();
		$form->set_action('dealnote_valid_popup.php');
		$form->set_method('POST');
		$this->formbuilder_deal_id_hidden($form);
		$form->start_section("form_row");
			$this->formbuilder_note($form);
		$form->end_section();
		$form->add_submit();
		return $form;
	}
	
	/*
	 * ------------------------------------------------------------
	* Formbuilders - private
	* ------------------------------------------------------------
	*/
	private function formbuilder_note(formbuilder &$form, $editable=1){
		$form->add_textarea('note', '', 3, 50, $this->get_note(), 0, $editable);
	}
	
	private function formbuilder_deal_id_hidden(&$form){
		$form->add_hidden('deal_id', $this->deal_id);
	}
	

	/*
	 * ------------------------------------------------------------
	* PUBLIC MEMBER FUNCTIONS
	* ------------------------------------------------------------
	*/
	
	

	/*
	 * ------------------------------------------------------------
	* PRIVATE MEMBER FUNCTIONS
	* ------------------------------------------------------------
	*/
	

	/*
	 * ------------------------------------------------------------
	* PUBLIC STATIC FUNCTIONS
	* ------------------------------------------------------------
	*/
	
	public static function build_notes($deal_id){
		//get the notes for this deal
		$deal_id = intval($deal_id);
		$where = array('n.deal_id' => $deal_id);
		$whereclause = db_build_where($where);
		
		$sql = "
		SELECT
			CONCAT_WS(' ', u.name_first, u.name_last) person,
			posted,
			note
		FROM " . self::TABLE . " n
		INNER JOIN " . user::TABLE . " u ON u.id=n.user_id
		$whereclause
		ORDER BY n.id asc";
		
		$data = db_select_assoc_array($sql);
		
		if(!$data){
			return false;//there were no notes
		}
		
		$sprint_dealnote = "<div class='notecontainer'>\n\t<div class='dealnote'>%s</div><br/>\n\t<div class='noteuser'>%s</div> <div class='noteposted'>%s</div><br/>\n</div>";
		
		//begin output
		$a = array();
		//work through notes
		foreach($data as $row){
			$a[] = sprintf($sprint_dealnote, $row['note'], $row['person'],  gmt_to_local_pretty_time($row['posted']));
		}
		//end output
		$output = join("\n", $a);
		
		//return output
		return $output;
	}

	public static function retrieve_notes($deal_id) {
	    $deal_id = intval($deal_id);
	    $where = array('n.deal_id' => $deal_id);
	    $whereclause = db_build_where($where);
	    
	    $sql = "
		SELECT
			CONCAT_WS(' ', u.name_first, u.name_last) person,
			posted,
			note
		FROM " . self::TABLE . " n
		INNER JOIN " . user::TABLE . " u ON u.id=n.user_id
		$whereclause
		ORDER BY n.id asc";
		
		$data = db_select_assoc_array($sql);
		
		if(!$data){
		    return false;//there were no notes
		}
		return $data;
	}
	
	/*
	 * ------------------------------------------------------------
	* PRIVATE STATIC FUNCTIONS
	* ------------------------------------------------------------
	*/
	
}