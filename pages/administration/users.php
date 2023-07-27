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
	if(urldecode($_GET['id'] != $_SESSION['user_id']) && $_POST['id'] != $_SESSION['user_id']){
		bounce_admin();
	}
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
                    $_SESSION['error'] = 'Password not updated.';
                } else {
                    if (!$user->update_password()) {
                        $_SESSION['error'] = 'Password not updated.';
                    } else {
                        $_SESSION['success'] = 'Password updated successfully.';
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
                        'msg' => 'Invalid Data - Please review your entries below. Your old password is incorrect',
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
                        'msg' => 'Password updated successfully.',
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
				if(isset($_POST['affiliation'])) {
					$user->set_affiliation($_POST['affiliation']);
					if(!$user->update_one()) {
						$twig->addGlobal('error', 'Unable to update user.');
					} else {
						$company = new company();
						$_SESSION['FT']['affiliation'] = array();
						if(!in_array($user->get_company_id(), $_POST['affiliation'])) {			
							$company->make_me_into($user->get_company_id());
							$_SESSION['FT']['affiliation'][] = $company->to_array();
						}
						foreach($_POST['affiliation'] as $item) {
							$company->make_me_into($item);
							$_SESSION['FT']['affiliation'][] = $company->to_array();
						}
                        echo json_encode(array(
                            'msg' => 'Affiliations are updated successfully.',
                            'type' => 'success'
                        ));
						//header('Location: /admin/users');
					}
				} else {					
					if(!$user->validate_edit($_POST)) {
						$twig->addGlobal('error', 'Unable to validate form data.');
					} else {
						if(!$user->update_one()) {
							$twig->addGlobal('error', 'Unable to update user.');
						} else {
                            echo json_encode(array(
                                'msg' => 'User is updated successfully.',
                                'type' => 'success'
                            ));
							//header('Location: /admin/users');
						}
					}
				}
            } else {
                $twig->addGlobal('error', 'Unable to retrieve user ' . $id);
            }
        }
    }
} else if (isset($_POST['action']) && $_POST['action'] == 'insert') {
    $nonce = new nonce($_POST['nonce']);
    $result = $nonce->test_request($_POST);
    if ($result) {
        
        $user = new user();
        if (!$user->validate_new($_POST)){
            $twig->addGlobal('error', 'Username already in use.');
            echo json_encode(array(
                'msg' => 'Username already in use.',
                'type' => 'error'
            ));
        } else {
            if (!$user->insert_new()) {
                $twig->addGlobal('error', 'Unable to create new user.');
                echo json_encode(array(
                    'msg' => 'Unable to create new user.',
                    'type' => 'error'
                ));
            } else {
                echo json_encode(array(
                    'msg' => 'User created successfully.',
                    'type' => 'success'
                ));
            }
        }
    }
    
} else if (isset($_GET['id'])) {
    if (isset($_GET['action']) && $_GET['action'] == 'toggle') {
        $user = new user();
        $id = $_GET['id'];
        if ($user->make_me_into($id)) {
            if ($user->get_company_id() != $_SESSION['FT']['company_id']) {
                $_SESSION['error'] = 'You are not permitted to change this user.';
            } else {
                $user->toggle();
                if (!$user->update_one()) {
                    $_SESSION['error'] = 'Unable to change user status.';
                } else {
                    $_SESSION['success'] = 'User is updated successfully.';
                    //header('Location: /admin/users');
                }
            }
            header('Location: /admin/users');
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
        $twig->addGlobal('page_title', 'Edit User');
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
		$companies = company::get_active_companies();
		$affiliates = affiliate::get_affiliates_by_company($user->get_company_id());
		
		$ids = array();
		foreach($affiliates as $el) {
			$ids[] = $el['affiliate'];
			foreach($companies as $row) {
				if($el['affiliate'] == $row['id']) {
					$row['company_id'] = $el['company_id'];
					$row['affiliate_id'] = $el['id'];
					$affiliated[]= $row;
				}
			}
		}
		if(!in_array($user->get_company_id(), $ids)) {			
			$company = new company();
			$company->make_me_into($user->get_company_id());
			$self = $company->to_array();
			$self['val'] = $self['name'];
			$affiliated = array_merge(array($self), $affiliated);
		}
		
		$twig->addGlobal('affiliates', $affiliated);
        /*
         * Render Templates
         */
        echo $twig->render('administration/tpl.user.twig');
    }
} else if (isset($_GET['action']) && $_GET['action'] == 'new') {
    $twig->addGlobal('page_title', 'New User');
    $twig->addGlobal('session', $_SESSION['FT']);
    $twig->addGlobal('action', 'insert');
    $twig->addGlobal('sec_support', $_SESSION['FT']['SEC_SUPPORT']);
    $twig->addGlobal('sec_admin', $_SESSION['FT']['SEC_ADMIN']);

    $nonce = new nonce();
    $nonce->add('action', 'insert');
    $nonce->add('session', $_SESSION['FT']);
    $nonce->add('company_id', $_SESSION['FT']['company_id']);
    $nonce->save();
	
	$companies = company::get_active_companies();
	$affiliates = affiliate::get_affiliates_by_company($_SESSION['FT']['company_id']);
	foreach($affiliates as $el) {
		foreach($companies as $row) {
			if($el['affiliate'] == $row['id']) {
				$row['company_id'] = $el['company_id'];
				$row['affiliate_id'] = $el['id'];
				$affiliated[]= $row;
			}
		}
	}
	$twig->addGlobal('affiliates', $affiliated);

    $twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());

    /*
     * Render Templates
     */

    echo $twig->render('administration/tpl.user.twig');
} else if(isset($_GET['dropdown'])) {
    if(isset($_POST['status']) && $_POST['status'] == 'all' || $_POST['status'] == 'active' || $_POST['status'] == 'inactive') {
        $_SESSION['FT']['users_dropdown'] = $_POST['status'];
    }
} else if(isset($_GET['table-list'])) {
    $controller = new usercontroller();

    echo $controller->getTableList($_GET, $_SESSION['FT']['users_dropdown'], $_SESSION['FT']['company_id']);
} else {
    $twig->addGlobal('page_title', 'Users');
    $twig->addGlobal('session', $_SESSION['FT']);
    $twig->addGlobal('error', $_SESSION['error']);
    $twig->addGlobal('success', $_SESSION['success']);
    unset($_SESSION['error']);
    unset($_SESSION['success']);
    
    // $controller = new usercontroller();
    // $output = $controller->invoke();
    // $twig->addGlobal('users', $output);
    
    if(!isset($_SESSION['FT']['users_dropdown'])) {
        $_SESSION['FT']['users_dropdown'] = "active";
    }
    $twig->addGlobal('users_dropdown', $_SESSION['FT']['users_dropdown']);

    // $controller = new usercontroller();
    // $output = $controller->invoke();
    // $twig->addGlobal('users', $output);

    /*
     * Render Templates
     */
    echo $twig->render('administration/tpl.users.twig');
}