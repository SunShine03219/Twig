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
            $toggle->set_table('flooring');
            if ($toggle->make_me_into($id)) {
                if (!$toggle->validate_edit($_POST)) {
                    $_SESSION['error'] = 'Flooring Company already exist with the same name.';
                } else {
                    if (!$toggle->update_one()) {
                        $_SESSION['error'] = 'Unable to update Flooring Company.';
                    } else {
                        $_SESSION['success'] = 'Flooring Company is updated successfully.';
                    }
                }
            } else {
                $_SESSION['error'] = 'Unable to retrieve user ' . $id;
            }
            header('Location: /admin/flooring');
            // $twig->addGlobal('page_title', 'Flooring Companies');
            // $twig->addGlobal('session', $_SESSION['FT']);
            // $sql = "SELECT * from flooring where company_id = ".$_SESSION['FT']['company_id']." AND active=1";
            // $output = db_select_assoc_array($sql);
            // $twig->addGlobal('companies', $output);
            // echo $twig->render('administration/tpl.flooring.twig');
        }
    }
} else if (isset($_POST['action']) && $_POST['action'] == 'insert') {
    $nonce = new nonce($_POST['nonce']);
    $result = $nonce->test_request($_POST);
    if($result){
        $toggle = new toggle();
        $toggle->set_table('flooring');
        if (!$toggle->validate_new($_POST)) {
            $_SESSION['error'] = 'Name already exist, Please Add a different one.';
            //header('Location: /admin/sales');
            //echo $twig->render('administration/tpl.sales.twig');
        } else {
            if (!$toggle->insert_new()) {
                $_SESSION['error'] = 'Unable to add Flooring Company.';
            } else {
                $_SESSION['success'] = 'Flooring Company added successfully.';
                //header('Location: /admin/sales');
            }
        }
        header('Location: /admin/flooring');
        // $twig->addGlobal('page_title', 'Flooring Companies');
        // $twig->addGlobal('session', $_SESSION['FT']);
        // $sql = "SELECT * from flooring where company_id = ".$_SESSION['FT']['company_id']." AND active=1";
        // $output = db_select_assoc_array($sql);
        // $twig->addGlobal('companies', $output);
        // echo $twig->render('administration/tpl.flooring.twig');
    }
    
} else if (isset($_GET['id'])) {
    if (isset($_GET['action']) && $_GET['action'] == 'toggle') {
        $flooring = new flooring();
        $id = $_GET['id'];
        if ($flooring->make_me_into($id)) {
            if ($flooring->get_company_id() != $_SESSION['FT']['company_id']) {
                $twig->addGlobal('error', 'You are not permitted to change this flooring company.');
            } else {
                $flooring->toggle();
                if (!$flooring->update_one()) {
                    $twig->addGlobal('error', 'Unable to change flooring company status.');
                } else {
                    header('Location: /admin/flooring');
                }
            }
        }
    } else {
        $twig->addGlobal('page_title', 'Edit Flooring Companies');
        $twig->addGlobal('session', $_SESSION['FT']);

        $flooring = new flooring();
        $id = $_GET['id'];
        if($flooring->make_me_into($id)) {
            $nonce = new nonce();
            $nonce->add('action', 'update');
            $nonce->add('id', $_GET['id']);
            $nonce->add('company_id', $_SESSION['FT']['company_id']);
            $nonce->save();
            
            $twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());
            $twig->addGlobal('flooringCompany', $flooring->to_array());
        } else {
            $twig->addGlobal('error', 'Flooring company could not be located.');
        }

        /*
         * Render Templates
         */
        echo $twig->render('administration/tpl.flooring-company.twig');
    }
} else if (isset($_GET['action']) && $_GET['action'] == 'new') {
    $twig->addGlobal('page_title', 'New Flooring Company');
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
    echo $twig->render('administration/tpl.flooring-company.twig');
} else if(isset($_GET['dropdown'])) {
    if(isset($_POST['status']) && $_POST['status'] == 'all' || $_POST['status'] == 'active' || $_POST['status'] == 'inactive') {
        $_SESSION['FT']['flooring_dropdown'] = $_POST['status'];
    }
} else if(isset($_GET['table-list'])) {
    $controller = new togglecontroller('flooring');

    echo $controller->getTableList($_GET);
} else {
    $twig->addGlobal('page_title', 'Flooring Companies');
    $twig->addGlobal('session', $_SESSION['FT']);
    $twig->addGlobal('error', $_SESSION['error']);
    $twig->addGlobal('success', $_SESSION['success']);
    unset($_SESSION['error']);
    unset($_SESSION['success']);
    
    $controller = new togglecontroller('flooring');
    $output = $controller->invoke();
    $twig->addGlobal('companies', $output);

    if(!isset($_SESSION['FT']['flooring_dropdown'])) {
        $_SESSION['FT']['flooring_dropdown'] = "active";
    }
    $twig->addGlobal('flooring_dropdown', $_SESSION['FT']['flooring_dropdown']);
    
    //var_dump($output);
    //var_dump($_SESSION['FT']);
    
    /*
     * Render Templates
     */
    echo $twig->render('administration/tpl.flooring.twig');
}