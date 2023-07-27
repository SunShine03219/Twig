<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();
bounce_support();
if (isset($_GET['id']) || isset($_POST['id'])) {

    $twig->addGlobal('page_title', 'Edit Messages');
    $twig->addGlobal('session', $_SESSION['FT']);

    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $messages = new messages();
        
        if($messages->make_me_into($id)){
            $nonce = new nonce();
            $nonce->add('action', 'update');
            $nonce->add('id', $_GET['id']);
            $nonce->save();
            $twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());
            $twig->addGlobal('message', $messages->to_array());
            
            
        } else {
            $twig->addGlobal('error', 'Unable to retrieve messages ' . $id);
        }

    } else {
       

        $nonce = new nonce($_POST['nonce']);
        $result = $nonce->test_request($_POST);
        if($result) {
            $id = intval($_POST['id']);
            $messages = new messages();
            if($messages->make_me_into($id)){
                if(!$messages->validate($_POST)){
                    $err = "";
                    if(isset($_SESSION['FT']['postback_errors']) && count($_SESSION['FT']['postback_errors']) > 0){
                        foreach($_SESSION['FT']['postback_errors'] as $e){
                            $err .= $e . ", ";
                        }
                        $err = substr($err, 0, -2);
                    }
                    $twig->addGlobal('error', $err);
                    $nonce = new nonce();
                    $nonce->add('action', 'update');
                    $nonce->add('id', $id);
                    $nonce->save();
                    $twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());
                    $twig->addGlobal('message', $messages->to_array());
                } else {
                    if(!$messages->update_one()){
                        $twig->addGlobal('error', 'Unable to update messages.');
                    } else {
                        header('Location: /messages/notifications');
                    }
                }
            } else {
                $twig->addGlobal('error', 'Unable to retrieve messages ' . $id);
            }
        }
        unset($nonce);
    }

    /*
     * Render Templates
     */
    echo $twig->render('messages/tpl.notification.twig');
} else if (isset($_GET['action']) && $_GET['action'] == 'new') {
    $twig->addGlobal('page_title', 'New messages');
    $twig->addGlobal('session', $_SESSION['FT']);
    $twig->addGlobal('action', 'insert');

    $nonce = new nonce();
    $nonce->add('action', 'insert');
    $nonce->save();

    $twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());

    /*
     * Render Templates
     */
    echo $twig->render('messages/tpl.notification.twig');
} else if (isset($_POST['action']) && $_POST['action'] == 'insert') {
    $messages = new messages();
    
    $nonce = new nonce();
    $result = $nonce->test_request($_POST);
	if ($result) {
        if(!$messages->validate($_POST)) {
            $twig->addGlobal('error', 'Unable to validate messages data.');
        } else {
            if(!$messages->insert_new()){
                $twig->addGlobal('error', 'Unable to create messages.');
            } else {				
				header('Location: /messages/notifications');
			}
        }
    }
	echo $twig->render('messages/tpl.notifications.twig');
} else if (isset($_GET['toggle'])) {
	$id = intval($_GET['toggle']);
	$messages = new messages();
	if($messages->make_me_into($id)){
		$messages->toggle();
		if(!$messages->update_one()){
			$twig->addGlobal('error', 'Unable to update messages.');
		} else {
			header('Location: /messages/notifications');
		}
	} else {
		$twig->addGlobal('error', 'Unable to retrieve messages ' . $id);
	}
	echo $twig->render('messages/tpl.notifications.twig');
} else if (isset($_GET['delete'])) {
	$id = intval($_GET['delete']);
	$messages = new messages();
	if($messages->make_me_into($id)){
		if(!$messages->delete_one()){
			$twig->addGlobal('error', 'Unable to delete messages.');
		} else {
			header('Location: /messages/notifications');
		}
	} else {
		$twig->addGlobal('error', 'Unable to retrieve messages ' . $id);
	}
	echo $twig->render('messages/tpl.notifications.twig');
} else {
    $twig->addGlobal('page_title', 'Meessages Log');
    $twig->addGlobal('session', $_SESSION['FT']);
	if(isset($_GET['status'])) {
		$twig->addGlobal('status', $_GET['status']);
	}
    $controller = new messages();
    $output = $controller->invoke();
    $twig->addGlobal('messages', $output);

    /*
     * Render Templates
     */
    echo $twig->render('messages/tpl.notifications.twig');
}
