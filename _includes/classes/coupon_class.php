<?php

class coupon{

	/*
	 * ------------------------------------------------------------
	 * VARIABLES
	 * ------------------------------------------------------------
	 */
	private $id;
	private $title;
    private $coupon;
	private $start_date;
	private $end_date;
	private $user_allowed;
	private $user_used;
	private $no_limit;
	private $coupon_type;
	private $coupon_value;
	private $status;

	const TABLE = 'coupons';
	 
	const title = 100;
	const coupon = 30;

	const RESET_ACTIVE = 0;
	const RESET_USED = 1;
	const RESET_EXPIRED = 2;
	
	

	 
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
		$this->coupon = "";
		$this->start_date = "";
		$this->end_date = "";
		$this->user_allowed = 0;
		$this->user_used = 0;
		$this->no_limit = 0;
		$this->coupon_type = 0;
		$this->coupon_value = 0;
		$this->status = 0;
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
        $this->set_coupon($data['coupon']);
		$this->set_start_date($data['start_date']);
		$this->set_end_date($data['end_date']);
		$this->set_user_allowed($data['user_allowed']);
		$this->set_user_used($data['user_used']);
		$this->set_no_limit((isset($data['no_limit']) && $data['no_limit'] != 0) ? 1 : 0);
		$this->set_coupon_type($data['coupon_type']);
		$this->set_coupon_value($data['coupon_value']);
		$this->set_status((isset($data['status']) && $data['status'] != 0) ? 1 : 0);
		
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

	public function get_title(){
		return $this->title;
	}

	public function get_coupon(){
		return $this->coupon;
	}

	public function get_start_date(){
		return $this->start_date;
	}

	public function get_end_date(){
		return $this->end_date;
	}

	public function get_user_allowed(){
		return $this->user_allowed;
	}

	public function get_user_used(){
		return $this->user_used;
	}

	public function get_no_limit(){
		return $this->no_limit;
	}

	public function get_coupon_type(){
		return $this->coupon_type;
	}

	public function get_coupon_value(){
		return $this->coupon_value;
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

    public function set_coupon($v){
        if($v){
            $this->coupon = $v;
        }
    }

	public function set_start_date($v){
		if($v){
			$this->start_date = $v;
		}
	}

	public function set_end_date($v){
		if($v){
			$this->end_date = $v;
		}
	}

	public function set_user_allowed($v){
		if($v){
			$this->user_allowed = $v;
		}
	}

	public function set_user_used($v){
		if($v){
			$this->user_used = $v;
		}
	}

	public function set_no_limit($v){
        $this->no_limit = $v;
	}

	public function set_coupon_type($v){
		if($v){
			$this->coupon_type = $v;
		}
	}

	public function set_coupon_value($v){
		if($v){
			$this->coupon_value = $v;
		}
	}

	public function set_status($v){
		$this->status = $v;
	}

	 
	 
	 
	 
	/*
	 * ------------------------------------------------------------
	 * TO_ARRAY
	 * ------------------------------------------------------------
	 */

	public function update_customer_key($user_id, $customer_key){
		$where = array('id' => $user_id);
		$data = ['customer_key'=> $customer_key];
		$result = db_update(self::TABLE, $data, $where);
		return $result;
	}

	public function to_array(){
		$myarray = array();
		$myarray['id'] = $this->id;
		$myarray['title'] = $this->title;
		$myarray['coupon'] = $this->coupon;
		$myarray['start_date'] = $this->start_date;
		$myarray['end_date'] = $this->end_date;
		$myarray['user_allowed'] = $this->user_allowed;
		$myarray['user_used'] = $this->user_used;
		$myarray['no_limit'] = $this->no_limit;
		$myarray['coupon_type'] = $this->coupon_type;
		$myarray['coupon_value'] = $this->coupon_value;
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
        $this->set_coupon($data['coupon']);
		$this->set_start_date(date('Y-m-d', strtotime($data['start_date'])));
		$this->set_end_date(date('Y-m-d', strtotime($data['end_date'])));
		$this->set_user_allowed($data['user_allowed']);
		$this->set_user_used($data['user_used']);
		$this->set_no_limit(isset($data['no_limit']) ? 1 : 0);
		$this->set_coupon_type($data['coupon_type']);
		$this->set_coupon_value($data['coupon_value']);
		$this->set_status(isset($data['status']) ? 1 : 0);
	}
	 
	 
	/*
	 * ------------------------------------------------------------
	 * VALIDATE_NEW
	 * ------------------------------------------------------------
	 */
	

	
	public function validate_edit(&$data){
		$error_flag = false;

		//no title and password change here
        if(empty($data['title'])){
            $error_flag = true;
            add_postback_error('title', 'Title cannot be blank');
        } else {
            $this->set_title($data['title']);
        }
		
		//coupon req'd
		if(empty($data['coupon'])){
			$error_flag = true;
			add_postback_error('coupon', 'Coupon cannot be blank');
		} elseif (strlen($data['coupon']) > 10){
			$error_flag = true;
			add_postback_error('coupon', 'Coupon cannot be longer than 10 characters');
		} else {
			$this->set_coupon($data['coupon']);
		}

		$this->set_start_date($data['start_date']);
		$this->set_end_date($data['end_date']);
		$this->set_user_allowed($data['user_allowed']);
		$this->set_user_used($data['user_used']);
		$this->set_no_limit($data['no_limit']);
		$this->set_coupon_type($data['coupon_type']);
		$this->set_coupon_value($data['coupon_value']);
		$this->set_status($data['status']);
		
		if($error_flag){
			set_postback_msg("Invalid Data - Please review your entries below");
			return false;
		} else {
			return true;
		}
	}
	
	public function validate_new(&$data){
		$error_flag = false;

		//title req'd unique across database
		//listen, I tried to make some constants and things to drop in here... it didn't work, so deal with magic numbers
		if(empty($data['title'])){
			$error_flag = true;
			add_postback_error('title', 'Title required');
		} else {
			$this->set_title($data['title']);
		}

		
		//coupon req'd
		if(empty($data['coupon'])){
			$error_flag = true;
			add_postback_error('coupon', 'Coupon cannot be blank');
		} elseif (strlen($data['coupon']) > 10){
			$error_flag = true;
			add_postback_error('coupon', 'Coupon cannot be longer than 10 characters');
		} elseif (db_exists(self::TABLE, 'coupon', $data['coupon'])){
            $error_flag = true;
            add_postback_error('coupon', 'Entered Coupon already exists');
        }else {
			$this->set_coupon($data['coupon']);
		}

		$this->set_start_date($data['start_date']);
		$this->set_end_date($data['end_date']);
		$this->set_user_allowed($data['user_allowed']);
		$this->set_user_used($data['user_used']);
		$this->set_no_limit($data['no_limit']);
		$this->set_coupon_type($data['coupon_type']);
		$this->set_coupon_value($data['coupon_value']);
		$this->set_status($data['status']);

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
     * ------------------------------------------------------------
     * DELETE_ONE
     * ------------------------------------------------------------
     */

    public function delete_one($id){
        $where = 'where id='. $id;
        $data = $this->to_array();
        unset($data['id']);
        $sql= 'delete from '.coupon::TABLE .' '.$where;
        $result = db_delete_bare($sql);
        return $result;
    }

    /*
     * ------------------------------------------------------------
     * GET COUPON by Coupon value
     * ------------------------------------------------------------
     */

    public function get_coupon_by_value($coupon){
        $where = 'where coupon="'. $coupon.'" and status="1"';
        $sql = 'select * from '.coupon::TABLE .' '.$where;
        $result = db_select_assoc($sql);
        return $result;
    }
	
	/*
	 * ------------------------------------------------------------
	 * Formbuilders - public
	 * ------------------------------------------------------------
	 */
	
	//used on view table to create a new tester
	public function formbuilder_quick_insert(formbuilder &$form){
		$form->start_section("form_row");
			$form->add_subtitle('New Coupon');
		$form->end_section();
		$form->start_section("form_row");
			$form->add_custom("<a href='tester.php?action=new'>Create New</a>", "Create new Coupon");
		$form->end_section();
	}



	/*
	 * ------------------------------------------------------------
	 * Formbuilders - private groups
	 * ------------------------------------------------------------
	 */

	// private function formbuilder_versioning(formbuilder &$form){
	// 	$this->formbuilder_id_hidden($form);
	// }




	/*
	 * ------------------------------------------------------------
	 * Formbuilders - private
	 * ------------------------------------------------------------
	 */
	
	// private function formbuilder_id_hidden(formbuilder &$form){
 //        $form->add_hidden('id', $this->id);
 //    }

	// private function formbuilder_username(formbuilder  &$form, $editable = 1){
	//  	$form->add_text('name', 'Name', $this->get_name(), 1, $editable);
	//  }

	//  private function formbuilder_email(formbuilder &$form, $editable = 1){
	//  	$form->add_text('email', 'E-Mail', $this->get_email(), 1, $editable);
	//  }
	 
	//  private function formbuilder_phone(formbuilder &$form, $editable = 1){
	//  	$form->add_text('phone', 'Phone', $this->get_phone(), 0, $editable);
	//  }
	 
	
	 
}