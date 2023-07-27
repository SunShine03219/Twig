<?php
    class forgot{
        
        private $errors;
        
        public function __construct(){
            $this->errors = array();
        }

        public function invoke()
        {
            $request = new genericrequest();
            switch($request->get_mode()){
                case 'get':
                    return $this->forgotform($request);
                    break;
                case 'post':
                    $request->add_array($_POST);
                    return $this->send_message($request);
                    break;
                default:
                    return $this->build_error('Unsupported Request');
            }
        }

        public function forgotform($request)
        {
            echo "hahaha";
        }

        public function send_message($request)
        {
			$code = strrev(rand(100000, 999999));
			$_SESSION['reset_pwd'] = array(
				'code'=>$code,
				'time'=>time(),
				'email'=>$request->username
			);
            mail($request->username, "Reset password code", "This is activation code for password change");
        }
    }

?>