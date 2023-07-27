<?php
    class verify{
        
        var $error;
        
        public function __construct(){
            $this->error = '';
        }

        public function reset_pwd()
        {
            $data = $_SESSION['reset_pwd'];
			if(!isset($data['code']) OR !isset($data['time'])) {
				$this->error = 'There is no verify code!';
			} else if(!isset($_POST['verify_code'])) {
				$this->error = 'Please enter your verify code!';
			} else if($_POST['verify_code'] != $data['code']) {
				$this->error = 'Your code is not matched!';
			} else if(time() - $data['time'] > 3600000) {
				$this->error = 'Your code was expired!';
			} else {
				return true;
			}
			
			if($this->error != '') {
				return false;
			}
        }
    }

?>