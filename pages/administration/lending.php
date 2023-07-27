<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();
bounce_admin();

if (isset($_POST['id'])) {
    if (isset($_POST['action']) && $_POST['action'] == 'update') {
        $nonce = new nonce($_POST['nonce']);
        $result = $nonce->test_request($_POST);
        if ($result) {
            $id = intval($_POST['id']);
            // $toggle = new toggle();
            // $toggle->set_table('lenders');
            $lender = new lender();
            if ($lender->make_me_into($id)) {
                if (!$lender->validate_edit($_POST)) {
                    $_SESSION['error'] = (isset($_SESSION['FT']['postback_errors']['name'])) ? $_SESSION['FT']['postback_errors']['name'] : '';
                } else {
                    if (!$lender->update_one()) {
                        $_SESSION['error'] = 'Unable to update Lender.';
                    } else {
                        $_SESSION['success'] = 'Lender is updated successfully.';
                        //header('Location: /admin/sales');
                    }
                }
            } else {
                $_SESSION['error'] = 'Unable to retrieve Lender ' . $id;
            }
            // $twig->addGlobal('page_title', 'Lending Sources');
            // $twig->addGlobal('session', $_SESSION['FT']);
            // $sql = "SELECT id, name, active, IF(active>0, 'Active', 'Inactive') as status from lenders where company_id = ".$_SESSION['FT']['company_id']." AND active=1 ORDER BY name ASC";
            // $output = db_select_assoc_array($sql);
            // $twig->addGlobal('lenders', $output);
            // echo $twig->render('administration/tpl.lending.twig');
        }
        header('Location: /admin/lending');
    }
} else if (isset($_POST['action']) && $_POST['action'] == 'insert') {
    $nonce = new nonce($_POST['nonce']);
    $result = $nonce->test_request($_POST);
    if($result){
        // $toggle = new toggle();
        // $toggle->set_table('lenders');
        $lender = new lender();
        if (!$lender->validate_new($_POST)) {
            $twig->addGlobal('error', (isset($_SESSION['FT']['postback_errors']['name'])) ? $_SESSION['FT']['postback_errors']['name'] : '');
        } else {
            if (!$lender->insert_new()) {
                $_SESSION['error'] = 'Unable to add Lender.';
            } else {
                $_SESSION['success'] = 'Lender added successfully.';
            }
        }
        // $twig->addGlobal('page_title', 'Lending Sources');
        // $twig->addGlobal('session', $_SESSION['FT']);
        // $sql = "SELECT id, name, active, IF(active>0, 'Active', 'Inactive') as status from lenders where company_id = ".$_SESSION['FT']['company_id']." AND active=1 ORDER BY name ASC";
        // $output = db_select_assoc_array($sql);
        // $twig->addGlobal('lenders', $output);
        // echo $twig->render('administration/tpl.lending.twig');
    }
    header('Location: /admin/lending');
} else if (isset($_GET['id'])) {
    if (isset($_GET['action']) && $_GET['action'] == 'toggle') {
        $lender = new lender();
        $id = $_GET['id'];
        if ($lender->make_me_into($id)) {
            if ($lender->get_company_id() != $_SESSION['FT']['company_id']) {
                $twig->addGlobal('error', 'You are not permitted to change this lender.');
            } else {
                $lender->toggle();
                if (!$lender->update_one()) {
                    $twig->addGlobal('error', 'Unable to change lender status.');
                } else {
                    header('Location: /admin/lending');
                }
            }
        }
    } else {
        $twig->addGlobal('page_title', 'Edit Lending Sources');
        $twig->addGlobal('session', $_SESSION['FT']);

        $lender = new lender();
        $id = $_GET['id'];
        if($lender->make_me_into($id)) {
            $nonce = new nonce();
            $nonce->add('action', 'update');
            $nonce->add('id', $_GET['id']);
            $nonce->add('company_id', $_SESSION['FT']['company_id']);
            $nonce->save();
            
            $twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());
            $twig->addGlobal('lender', $lender->to_array());
        } else {
            $twig->addGlobal('error', 'Lender could not be located.');
        }

        /*
         * Render Templates
         */
        echo $twig->render('administration/tpl.lender.twig');
    }
} else if (isset($_GET['action']) && $_GET['action'] == 'new') {
    $twig->addGlobal('page_title', 'New Lender');
    $twig->addGlobal('session', $_SESSION['FT']);
    $twig->addGlobal('action', 'insert');
    
    $nonce = new nonce();
    $nonce->add('action', 'insert');
    $nonce->add('company_id', $_SESSION['FT']['company_id']);
    $nonce->save();

    $twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());
    
    /*
     * Render Templates
     */
    echo $twig->render('administration/tpl.lender.twig');
}  else if(isset($_GET['dropdown'])) {
    if(isset($_POST['status']) && $_POST['status'] == 'all' || $_POST['status'] == 'active' || $_POST['status'] == 'inactive') {
        $_SESSION['FT']['lender_dropdown'] = $_POST['status'];
    }
} else if(isset($_GET['table-list'])) {
    $controller = new togglecontroller('lender');

    echo $controller->getTableList($_GET);
} else {
    $twig->addGlobal('page_title', 'Lending Sources');
    $twig->addGlobal('session', $_SESSION['FT']);
    $twig->addGlobal('error', $_SESSION['error']);
    $twig->addGlobal('success', $_SESSION['success']);
    unset($_SESSION['success']);
    unset($_SESSION['error']);
    
    $controller = new togglecontroller('lender');
    $output = $controller->invoke();
    $twig->addGlobal('lenders', $output);

    if(!isset($_SESSION['FT']['lender_dropdown'])) {
        $_SESSION['FT']['lender_dropdown'] = "active";
    }
    $twig->addGlobal('lender_dropdown', $_SESSION['FT']['lender_dropdown']);
    
    //PrintVar($output);exit;
    //var_dump($_SESSION['FT']);
    
    /*
     * Render Templates
     */
    echo $twig->render('administration/tpl.lending.twig');
}