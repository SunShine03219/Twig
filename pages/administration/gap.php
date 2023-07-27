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
            $toggle = new toggle();
            $toggle->set_table('gapproviders');
            if ($toggle->make_me_into($id)) {
                if (!$toggle->validate_edit($_POST)) {
                    $_SESSION['error'] = 'Gap Provider already exist with the same name.';
                } else {
                    if (!$toggle->update_one()) {
                        $_SESSION['error'] = 'Unable to update Gap Provider.';
                    } else {
                        $_SESSION['success'] = 'Gap Provider is updated successfully.';
                        //header('Location: /admin/gap');
                    }
                }
            } else {
                $_SESSION['error'] = 'Unable to retrieve Gap Provider ' . $id;
            }
            header('Location: /admin/gap');
            // $twig->addGlobal('page_title', 'Gap Providers');
            // $twig->addGlobal('session', $_SESSION['FT']);
            // $sql = "SELECT * from gapproviders where company_id = ".$_SESSION['FT']['company_id']." AND active=1";
            // $output = db_select_assoc_array($sql);
            // $twig->addGlobal('providers', $output);

            // if(!isset($_SESSION['FT']['gapproviders_dropdown'])) {
            //     $_SESSION['FT']['gapproviders_dropdown'] = "active";
            // }
            // $twig->addGlobal('gapproviders_dropdown', $_SESSION['FT']['gapproviders_dropdown']);
            // echo $twig->render('administration/tpl.gap.twig');
        }
    }
} else if (isset($_POST['action']) && $_POST['action'] == 'insert') {
    $nonce = new nonce($_POST['nonce']);
    $result = $nonce->test_request($_POST);
    if($result){
        $toggle = new toggle();
        $toggle->set_table('gapproviders');
        if (!$toggle->validate_new($_POST)) {
            $_SESSION['error'] = 'Name already exist, Please Add a different one.';
            //header('Location: /admin/sales');
            //echo $twig->render('administration/tpl.sales.twig');
        } else {
            if (!$toggle->insert_new()) {
                $_SESSION['error'] = 'Unable to add Gap Provider.';
            } else {
                $_SESSION['success'] = 'Gap Provider added successfully.';
                //header('Location: /admin/sales');
            }
        }
        header('Location: /admin/gap');
        // $twig->addGlobal('page_title', 'Gap Provider');
        // $twig->addGlobal('session', $_SESSION['FT']);

        // if(!isset($_SESSION['FT']['gapproviders_dropdown'])) {
        //     $_SESSION['FT']['gapproviders_dropdown'] = "active";
        // }
        // $sql = "SELECT * from gapproviders where company_id = ".$_SESSION['FT']['company_id']." AND active=1";
        // $output = db_select_assoc_array($sql);
        // $twig->addGlobal('providers', $output);
        // $twig->addGlobal('gapproviders_dropdown', $_SESSION['FT']['gapproviders_dropdown']);
        // echo $twig->render('administration/tpl.gap.twig');
    }
    
} else if (isset($_GET['id'])) {
    if (isset($_GET['action']) && $_GET['action'] == 'toggle') {
        $gap = new gap();
        $id = $_GET['id'];
        if ($gap->make_me_into($id)) {
            if ($gap->get_company_id() != $_SESSION['FT']['company_id']) {
                $twig->addGlobal('error', 'You are not permitted to change this gap provider.');
            } else {
                $gap->toggle();
                if (!$gap->update_one()) {
                    $twig->addGlobal('error', 'Unable to change gap provider status.');
                } else {
                    header('Location: /admin/gap');
                }
            }
        }
    } else {
        $twig->addGlobal('page_title', 'Edit Gap Providers');
        $twig->addGlobal('session', $_SESSION['FT']);
        
        $gap = new gap();
        $id = $_GET['id'];
        if($gap->make_me_into($id)) {
            $nonce = new nonce();
            $nonce->add('action', 'update');
            $nonce->add('id', $_GET['id']);
            $nonce->add('company_id', $_SESSION['FT']['company_id']);
            $nonce->save();
            
            $twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());
            $twig->addGlobal('gapProvider', $gap->to_array());
        } else {
            $twig->addGlobal('error', 'Gap provider could not be located.');
        }
        
        /*
         * Render Templates
         */
        echo $twig->render('administration/tpl.gap-provider.twig');
    }
} else if (isset($_GET['action']) && $_GET['action'] == 'new') {
    $twig->addGlobal('page_title', 'New Gap Provider');
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
    echo $twig->render('administration/tpl.gap-provider.twig');
} else if(isset($_GET['dropdown'])) {
    if(isset($_POST['status']) && $_POST['status'] == 'all' || $_POST['status'] == 'active' || $_POST['status'] == 'inactive') {
        $_SESSION['FT']['gap_dropdown'] = $_POST['status'];
    }
} else if(isset($_GET['table-list'])) {
    $controller = new togglecontroller('gap');

    echo $controller->getTableList($_GET);
} else {
    $twig->addGlobal('page_title', 'Gap Providers');
    $twig->addGlobal('session', $_SESSION['FT']);
    $twig->addGlobal('error', $_SESSION['error']);
    $twig->addGlobal('success', $_SESSION['success']);
    unset($_SESSION['error']);
    unset($_SESSION['success']);
    
    $controller = new togglecontroller('gap');
    $output = $controller->invoke();
    $twig->addGlobal('providers', $output);

    if(!isset($_SESSION['FT']['gap_dropdown'])) {
            $_SESSION['FT']['gap_dropdown'] = "active";
        }
        $twig->addGlobal('gap_dropdown', $_SESSION['FT']['gap_dropdown']);
    
    //var_dump($output);
    //var_dump($_SESSION['FT']);
    
    /*
     * Render Templates
     */
    echo $twig->render('administration/tpl.gap.twig');
}