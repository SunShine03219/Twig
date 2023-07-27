<?php

class messages{


	/*
	 * ------------------------------------------------------------
	 * VARIABLES
	 * ------------------------------------------------------------
	 */
	
	private $id;
	private $title;
	private $date;
	private $text;
	private $status;
	 
	const TABLE = 'messages';
	const TABLE_READ_STATUS = 'message_read_status';
	
	 
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
	 
	private function zero_it(){
		$this->id = 0;
		$this->title = "";
		$this->date = "";
		$this->text = "";
		$this->status = "";

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

		if(!$data){ $this->zero_it(); return false; }

		$this->set_id($data['id']);
		$this->set_title($data['title']);
		$this->set_date($data['date']);
		$this->set_text($data['text']);
		$this->set_status($data['status']);
		
		return true;
	}
	 

	/*
	 * ------------------------------------------------------------
	 * MAKE_ME_INTO
	 * ------------------------------------------------------------
	 */
	 

	public function make_me_into($v){
		$this->set_id($v);
		return $this->populate();
	}
	 
	/*
	 * ------------------------------------------------------------
	 * SET_READ_STATUS
	 * ------------------------------------------------------------
	 */


	public function set_read_status($message_id, $status = 1){

		$user_id = $_SESSION['FT']['user_id'];
		
		$where = array('message_id' => $message_id, 'user_id' => $user_id);
		$data = db_select_one(self::TABLE_READ_STATUS, $where);
		
		if($data){
			$data = array();
			$data['status'] = $status;
			$where = array();
			if ($status) {
				$where = array('message_id' => $message_id, 'user_id' => $user_id);
			} else {
				$where = array('message_id' => $message_id);
			}
			db_update(self::TABLE_READ_STATUS, $data, $where);
		} else {
			$data = array();
			$data['user_id'] = $user_id;
			$data['message_id'] = $message_id;
			$data['status'] = $status;

			db_insert_assoc_one(self::TABLE_READ_STATUS, $data);
		}
	}

	/*
	 * ------------------------------------------------------------
	 * ACCESSORS
	 * ------------------------------------------------------------
	 */
	 

	public function get_id(){
		return $this->id;
	}

	public function get_title(){
		return $this->title;
	}

	public function get_text(){
		return $this->text;
	}

	public function get_status(){
		return $this->status;
	}

	/*
	 * ------------------------------------------------------------
	 * MODIFIERS
	 * ------------------------------------------------------------
	 */

	public function set_id($v){
		if($v){
			$this->id = $v;
		}
	}

	public function set_title($v){
		if($v){
			$this->title = $v;
		}
	}

	public function set_text($v){
		if($v){
			$this->text = $v;
		}
	}
	public function set_date($v){
		if($v){
			$this->date = $v;
		}
	}

	public function set_status($v){
		if($v){
            $this->status = 1;
        }else{
            $this->status = 0;
        }
	}

	public function toggle(){
		$status = $this->get_status();
		if ($status) {
			$this->set_status(0);
		} else {
			$this->set_status(1);
			$this->set_read_status($this->id, 0);
		}
	}
	 
	/*
	 * ------------------------------------------------------------
	 * BUILD_FORM
	 * ------------------------------------------------------------
	 */
	 
	 
	 
	 
	/*
	 * ------------------------------------------------------------
	 * TO_ARRAY
	 * ------------------------------------------------------------
	 */

	public function to_array(){
		$myarray = array();
		$myarray['id'] = $this->id;
		$myarray['title'] = $this->title;
		$myarray['text'] = $this->text;
    $date = new DateTime($this->date);
		$myarray['date'] = $date->format('Y-m-d');
		$myarray['status'] = $this->status;
		return $myarray;
	}
	
	
	/*
	 * ------------------------------------------------------------
	 * FROM_ARRAY
	 * ------------------------------------------------------------
	 */

	public function from_array(&$data){
		$this->set_id($data['id']);
		$this->set_title($data['title']);
		$this->set_text($data['text']);
		$this->set_date($data['date']);
		$this->set_status($data['status']);
	}
	 
	/*
	 * ------------------------------------------------------------
	 * VALIDATE_NEW
	 * ------------------------------------------------------------
	 */
	
	public function validate(&$data){
		$error_flag = false;
		//name *req'd, unique
		if(empty($data['title'])){
			$error_flag = true;
			add_postback_error('title', 'Message title required');
		} else {
			$this->set_title($data['title']);
		}
		
		//referral *req'd
		if(empty($data['text'])){
			$error_flag = true;
			add_postback_error('text', 'Message required');
		} else {
			$this->set_text($data['text']);
		}

        

        if(empty($data['date'])){
			$error_flag = true;
			add_postback_error('date', 'The date cannot be empty');
		} else {
            $date_now = new DateTime();
            $date_defined    = new DateTime($data['date']); 
            $interval = $date_now->diff($date_defined);
            
            if($interval->format('%R%a') < 0){
                $error_flag = true;
			    add_postback_error('date', 'The date cannot be less than the current day');
            }else{
                $this->set_date($data['date']);
            }
		}
        
        if(!isset($data['status']) || empty($data['status'])){
			$this->set_status(0);
		} else {
			$this->set_status($data['status']);
		}
		if($error_flag){
			set_postback_msg("Invalid Data - Please review your entries below");
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
	 * UPDATE_ONE
	 * ------------------------------------------------------------
	 */
	 

	public function update_one(){
		$where = array('id' => $this->id);
		$data = $this->to_array();
		unset($data['id']);
		
		$result = db_update(self::TABLE, $data, $where);
		
		return $result;
	}
	/* 
	 *-------------------------------------------------------------
	 * DELETE_ONE
	 *-------------------------------------------------------------
	 */
	public function delete_one() {
		$sql = "DELETE from " . self::TABLE . " WHERE id='" . $this->get_id() . "' ";
		db_delete_bare($sql);//yes, terrible, I know
		
		return true;
	}
	/*
	 * ------------------------------------------------------------
	 * PREPARE NEW
	 * ------------------------------------------------------------
	 */
	public function prepare_new(){
		//Don't think there is anything that I need to add for this in subscription
		
	}
	 
	 	
	/*
	 * ------------------------------------------------------------
	 * Formbuilders - public
	 * ------------------------------------------------------------
	 */
	
	public function formbuilder_quick_insert(formbuilder &$form){
		$form->start_section('form_row');
		$form->add_subtitle('New Message Log');
		$form->end_section();
	
		$form->start_section('form_row');
		$form->add_custom('<a href="messages.php?action=new">Create New</a>', 'Create new Message Log');
		$form->end_section();
	}
	
	/*
	 * ------------------------------------------------------------
	 * Formbuilders - private
	 * ------------------------------------------------------------
	 */
	 
	 private function formbuilder_id_hidden(&$form){
	 	$form->add_hidden('id', $this->get_id());
	 }
	 
	 private function formbuilder_name(&$form, $editable = 1){
	 	$form->add_text('title', 'Message title', $this->get_title(), 1, $editable);
	 }
	 
	 private function formbuilder_text(&$form, $editable = 1){
	 	$form->add_text('text', 'Text Message', $this->get_text(), 1, $editable);
	 }
	 
	 
	  	
	/*
	 * ------------------------------------------------------------
	 * PUBLIC MEMBER FUNCTIONS
	 * ------------------------------------------------------------
	 */
	 
	 public function invoke($_mode) {
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		
		switch($method){
			case 'post':
				return true;
				break;
				
			case 'get':
			    return $this->get_messages($_mode);
				break;
				
			default:
				return $this->build_error('Unsupported request method');
		}
	 }
	 
	 public function get_active_messages() {
		 
		 $array = db_select(self::TABLE, ['status' => 1]);
		 return $array;
	 }

	 private function get_messages($_mode) {
		if(isset($_mode)){
			$user_id = $_SESSION['FT']['user_id'];
			$sql = "SELECT m.id, m.title, m.text, s.status From ".self::TABLE." m
				LEFT JOIN ".self::TABLE_READ_STATUS." s ON s.user_id=$user_id and m.id=s.message_id";
			$array = db_select_assoc_array($sql);
			$data = [];
			if(!empty($array)){
					foreach($array as $var){
							$var['text'] = trim($var['text']);
							$data[] = $var;
					}
			}
			return $data;
		}else{
			$array = db_select(self::TABLE);
			$data = [];
			if(!empty($array)){
					foreach($array as $var){
							$dt = new DateTime($var['date']);
							$var['date'] = $dt->format('Y-m-d');
							$var['text'] = trim($var['text']);
							$data[] = $var;
					}
			}
			return $data;
		}

	}

	public function get_unread_messages(){
		$user_id = $_SESSION['FT']['user_id'];

		// check out dated messages and set inactive
		$date = new DateTime($this->date);
		$date = $date->format('Y-m-d');
		$sql = "update messages set status=0 where date<'$date'";
		db_update_bare($sql);

		// get unread messages
		$sql = "SELECT m.id, m.title, m.text, m.date From ".self::TABLE." m
			LEFT JOIN ".self::TABLE_READ_STATUS." s ON s.user_id=$user_id and m.id=s.message_id
			Where m.status=1 AND (s.status is null OR s.status=0)";

		$array = db_select_assoc_array($sql);
		
		$data = [];
		if(!empty($array)){
				foreach($array as $var){
						$var['text'] = trim($var['text']);
						$data[] = $var;
				}
		}
		return $data;
	}

	public function get_messages_day() {
		 
		$table = self::TABLE;
		$dt = new DateTime();

		$array = db_select_assoc_array("SELECT title, text, date, status FROM {$table} WHERE status = 1 AND date = '{$dt->format('Y-m-d')}' ORDER BY date; ");
        $data = [];
        if(!empty($array)){
            foreach($array as $var){
                $dt = new DateTime($var['date']);
                $var['date'] = $dt->format('Y-m-d');
                $var['text'] = trim($var['text']);
                $data[] = $var;
            }
        }
		return $data;
	}
	 	
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
	 
	
	/*
	 * ------------------------------------------------------------
	 * PRIVATE STATIC FUNCTIONS
	 * ------------------------------------------------------------
	 */
	 	
	
	
	
}