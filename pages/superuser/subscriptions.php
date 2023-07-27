<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();
bounce_support();
if (isset($_GET['id']) || isset($_POST['id'])) {

    
    $twig->addGlobal('page_title', 'Edit subscription');
    $twig->addGlobal('session', $_SESSION['FT']);

    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $subscription = new subscription();
        if($subscription->make_me_into($id)){

            $nonce = new nonce();
            $nonce->add('action', 'update');
            $nonce->add('id', $_GET['id']);
            $nonce->save();

            $twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());
            $twig->addGlobal('subscription', $subscription->to_array());
            
        } else {
            $twig->addGlobal('error', 'Unable to retrieve subscription ' . $id);
        }

    } else {
       

        $nonce = new nonce($_POST['nonce']);
        $result = $nonce->test_request($_POST);
        if($result) {
            $id = intval($_POST['id']);
            $subscription = new subscription();
            if($subscription->make_me_into($id)){
                if(!$subscription->validate($_POST)){
                    $twig->addGlobal('error', 'Unable to validate form data.');
                } else {
                    if(!$subscription->update_one()){
                        $twig->addGlobal('error', 'Unable to update subscription.');
                    } else {
                        header('Location: /superuser/subscriptions');
                    }
                }
            } else {
                $twig->addGlobal('error', 'Unable to retrieve subscription ' . $id);
            }
        }
        unset($nonce);
    }

    /*
     * Render Templates
     */
    echo $twig->render('superuser/tpl.subscription.twig');
} else if (isset($_GET['action']) && $_GET['action'] == 'new') {
    $twig->addGlobal('page_title', 'New subscription');
    $twig->addGlobal('session', $_SESSION['FT']);
    $twig->addGlobal('action', 'insert');

    $nonce = new nonce();
    $nonce->add('action', 'insert');
    $nonce->save();

    $twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());

    /*
     * Render Templates
     */
    echo $twig->render('superuser/tpl.subscription.twig');
} else if (isset($_POST['action']) && $_POST['action'] == 'insert') {
    $subscription = new subscription();
    
    $nonce = new nonce();
    $result = $nonce->test_request($_POST);
	if ($result) {
        if(!$subscription->validate($_POST)) {
            $twig->addGlobal('error', 'Unable to validate subscription data.');
        } else {
            if(!$subscription->insert_new()){
                $twig->addGlobal('error', 'Unable to create subscription.');
            } else {				
				header('Location: /superuser/subscriptions');
			}
        }
    }
	echo $twig->render('superuser/tpl.subscription.twig');
} else if (isset($_GET['toggle'])) {
	$id = intval($_GET['toggle']);
	$subscription = new subscription();
	if($subscription->make_me_into($id)){
		$subscription->toggle();
		if(!$subscription->update_one()){
			$twig->addGlobal('error', 'Unable to update subscription.');
		} else {
			header('Location: /superuser/subscriptions');
		}
	} else {
		$twig->addGlobal('error', 'Unable to retrieve subscription ' . $id);
	}
	echo $twig->render('superuser/tpl.subscriptions.twig');
} else if (isset($_GET['delete'])) {
	$id = intval($_GET['delete']);
	$subscription = new subscription();
	if($subscription->make_me_into($id)){
		if(!$subscription->delete_one()){
			$twig->addGlobal('error', 'Unable to delete subscription.');
		} else {
			header('Location: /superuser/subscriptions');
		}
	} else {
		$twig->addGlobal('error', 'Unable to retrieve subscription ' . $id);
	}
	echo $twig->render('superuser/tpl.subscriptions.twig');
} else {
    $twig->addGlobal('page_title', 'subscriptions');
    $twig->addGlobal('session', $_SESSION['FT']);
	if(isset($_GET['status'])) {
		$twig->addGlobal('status', $_GET['status']);
	}
    $controller = new subscription();
    $output = $controller->invoke();
    $twig->addGlobal('subscriptions', $output);

    /*
     * Render Templates
     */
    echo $twig->render('superuser/tpl.subscriptions.twig');
}
