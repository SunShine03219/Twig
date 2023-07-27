<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});
if(!isset($_SESSION['reset_pwd']['time']) OR (time()-$_SESSION['reset_pwd']['time']) > 3600000) {	
	bounce();
}

if (isset($_POST['id'])) {
    if (isset($_POST['action']) && $_POST['action'] == 'reset') {
        $nonce = new nonce($_POST['nonce']);
        $result = $nonce->test_request($_POST);
        if ($result) {
            $id = intval($_POST['id']);
            $user = new user();
            if ($user->make_me_into($id)) {
                if (!$user->validate_password($_POST)) {
                    $twig->addGlobal('error', 'Username already in use.');
                } else {
                    if (!$user->update_password()) {
                        $twig->addGlobal('error', 'Username already in use.');
                    } else {
                        header('Location: /admin/users');
                    }
                }
            } else {
                $twig->addGlobal('error', 'Unable to retrieve user ' . $id);
            }
        }
        if(isset($_SESSION['reset_pwd']['time'])) {
            unset($_SESSION['reset_pwd']);
        }
    } else if(isset($_POST['action']) && $_POST['action'] == 'reset_pwd'){
        $nonce = new nonce($_POST['nonce']);
        $result = $nonce->test_request($_POST);
        if ($result) {
            $id = intval($_POST['id']);
            $user = new user();
            if ($user->make_me_into($id)) {
                $isOldRight = $user->confirm_old_password($_POST['id'], $_POST['old_pass']);
                if (!$user->validate_update_password($_POST) || $isOldRight == 'error') {
                    //$twig->addGlobal('error', 'Invalid Data - Please review your entries below.');
                    echo json_encode(array(
                        'msg' => 'Invalid Data - Please review your entries below.',
                        'type' => 'error'
                    ));
                } else {
                    if (!$user->update_password()) {
                        //$twig->addGlobal('error', 'Invalid Data - Please review your entries below.');
                        echo json_encode(array(
                        'msg' => 'Invalid Data - Please review your entries below.',
                        'type' => 'error'
                    ));
                    } else {
                        echo json_encode(array(
                        'msg' => 'Password Updated.',
                        'type' => 'success'
                    ));
                        //header('Location: /admin/users');
                    }
                }
            } else {
                echo json_encode(array('msg' => 'User Not Found'));
                $twig->addGlobal('error', 'Unable to retrieve user ' . $id);
            }
        }
        if(isset($_SESSION['reset_pwd']['time'])) {
            unset($_SESSION['reset_pwd']);
        }
    }else {
        $nonce = new nonce($_POST['nonce']);
        $result = $nonce->test_request($_POST);
        if ($result) {
            $id = intval($_POST['id']);
            $user = new user();
            if ($user->make_me_into($id)) {		
				if(!$user->validate_edit($_POST)) {
					$twig->addGlobal('error', 'Unable to validate form data.');
				} else {
					if(!$user->update_one()) {
						$twig->addGlobal('error', 'Unable to update user.');
					} else {
						header('Location: /admin/users');
					}
				}
            } else {
                $twig->addGlobal('error', 'Unable to retrieve user ' . $id);
            }
        }
    }
} else if (isset($_GET['id'])) {
    if (isset($_GET['action']) && $_GET['action'] == 'toggle') {
        $user = new user();
        $id = $_GET['id'];
        if ($user->make_me_into($id)) {
            if ($user->get_company_id() != $_SESSION['FT']['company_id']) {
                $twig->addGlobal('error', 'You are not permitted to change this user.');
            } else {
                $user->toggle();
                if (!$user->update_one()) {
                    $twig->addGlobal('error', 'Unable to change user status.');
                } else {
                    header('Location: /admin/users');
                }
            }
        }
    } else if (isset($_GET['action']) && $_GET['action'] == 'reset') {
        $user = new user();
        $twig->addGlobal('page_title', 'Reset the password for ' . $user::get_full_name($_GET['id']));
        $twig->addGlobal('session', $_SESSION['FT']);

        $nonce = new nonce();
        $nonce->add('action', 'reset');
        $nonce->add('id', $_GET['id']);
        $nonce->save();

        $twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());

        /*
         * Render Templates
         */
        echo $twig->render('administration/tpl.user-password-reset.twig');
    } else {
        $twig->addGlobal('page_title', 'Update Profile');

        $twig->addGlobal('session', $_SESSION['FT']);
        $twig->addGlobal('sec_support', $_SESSION['FT']['SEC_SUPPORT']);
        $twig->addGlobal('sec_admin', $_SESSION['FT']['SEC_ADMIN']);

        $user = new user();
        $id = $_GET['id'];
        if($user->make_me_into($id)){
            $nonce = new nonce();
            $nonce->add('action', 'update');
            $nonce->add('id', $id);
            $nonce->add('company_id', $_SESSION['FT']['company_id']);
            $nonce->save();
            
			$users_arr = $user->to_array();
			$users_arr['affiliation'] = json_decode($users_arr['affiliation'], true);
			
            $twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());
            $twig->addGlobal('user', $users_arr);
        } else {
            $twig->addGlobal('error', 'User could not be located.');
        }
		// $companies = company::get_active_companies();
		// $affiliates = affiliate::get_affiliates_by_company($user->get_company_id());
		
		// $ids = array();
		// foreach($affiliates as $el) {
		// 	$ids[] = $el['affiliate'];
		// 	foreach($companies as $row) {
		// 		if($el['affiliate'] == $row['id']) {
		// 			$row['company_id'] = $el['company_id'];
		// 			$row['affiliate_id'] = $el['id'];
		// 			$affiliated[]= $row;
		// 		}
		// 	}
		// }
		// if(!in_array($user->get_company_id(), $ids)) {			
		// 	$company = new company();
		// 	$company->make_me_into($user->get_company_id());
		// 	$self = $company->to_array();
		// 	$self['val'] = $self['name'];
		// 	$affiliated = array_merge(array($self), $affiliated);
		// }
		
		// $twig->addGlobal('affiliates', $affiliated);
        /*
         * Render Templates
         */
        echo $twig->render('profile/tpl.profile.twig');
    }
} 