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
            $toggle->set_table('salespeople');
            if ($toggle->make_me_into($id)) {
                if (!$toggle->validate_edit($_POST)) {
                    $_SESSION['error'] = 'Salesperson already exist with the same name.';
                } else {
                    if (!$toggle->update_one()) {
                        $_SESSION['error'] = 'Unable to update Salesperson.';
                    } else {
                        $_SESSION['success'] = 'Salesperson is updated successfully.';
                        //header('Location: /admin/sales');
                    }
                }
            } else {
                $_SESSION['error'] = 'Unable to retrieve user ' . $id;
            }
            // $twig->addGlobal('page_title', 'Sale People');
            // $twig->addGlobal('session', $_SESSION['FT']);

            // if(!isset($_SESSION['FT']['salespeople_dropdown'])) {
            //     $_SESSION['FT']['salespeople_dropdown'] = "active";
            // }
            // $twig->addGlobal('salespeople_dropdown', $_SESSION['FT']['salespeople_dropdown']);
            // echo $twig->render('administration/tpl.sales.twig');
        }
        header('Location: /admin/sales');
    }
} else if (isset($_POST['action']) && $_POST['action'] == 'insert') {
    $nonce = new nonce($_POST['nonce']);
    $result = $nonce->test_request($_POST);
    if($result){
        $toggle = new toggle();
        $toggle->set_table('salespeople');
        if (!$toggle->validate_new($_POST)) {
            $_SESSION['error'] = 'Name already exist, Please Add a different one.';
            //header('Location: /admin/sales');
            //echo $twig->render('administration/tpl.sales.twig');
        } else {
            if (!$toggle->insert_new()) {
                $_SESSION['error'] = 'Unable to add Salesperson.';
            } else {
                $_SESSION['success'] = 'Salesperson added successfully.';
                // header('Location: /admin/sales');
            }
        }
        header('Location: /admin/sales');
        // $twig->addGlobal('page_title', 'Sale People');
        // $twig->addGlobal('session', $_SESSION['FT']);

        // if(!isset($_SESSION['FT']['salespeople_dropdown'])) {
        //     $_SESSION['FT']['salespeople_dropdown'] = "active";
        // }
        // $twig->addGlobal('salespeople_dropdown', $_SESSION['FT']['salespeople_dropdown']);
        // echo $twig->render('administration/tpl.sales.twig');
    }
    
} else if (isset($_GET['id'])) {
    if (isset($_GET['action']) && $_GET['action'] == 'toggle') {
        $sales = new sales();
        $id = $_GET['id'];
        if ($sales->make_me_into($id)) {
            if ($sales->get_company_id() != $_SESSION['FT']['company_id']) {
                $twig->addGlobal('error', 'You are not permitted to change this user.');
            } else {
                $sales->toggle();
                if (!$sales->update_one()) {
                    $twig->addGlobal('error', 'Unable to change salespeson status.');
                } else {
                    header('Location: /admin/sales');
                }
            }
        }
    } else {
        $twig->addGlobal('page_title', 'Edit Sales Person');
        $twig->addGlobal('session', $_SESSION['FT']);

        $sales = new sales();
        $id = $_GET['id'];
        if($sales->make_me_into($id)) {
            $nonce = new nonce();
            $nonce->add('action', 'update');
            $nonce->add('id', $_GET['id']);
            $nonce->add('company_id', $_SESSION['FT']['company_id']);
            $nonce->save();

            $twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());
            $twig->addGlobal('salesPerson', $sales->to_array());
        } else {
            $twig->addGlobal('error', 'Salesperson could not be located.');
        }

        /*
         * Render Templates
         */
        echo $twig->render('administration/tpl.salesperson.twig');
    }
} else if (isset($_GET['action']) && $_GET['action'] == 'new') {
    $twig->addGlobal('page_title', 'New Salesperson');
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
    echo $twig->render('administration/tpl.salesperson.twig');
} else if(isset($_GET['dropdown'])) {
    if(isset($_POST['status']) && $_POST['status'] == 'all' || $_POST['status'] == 'active' || $_POST['status'] == 'inactive') {
        $_SESSION['FT']['salespeople_dropdown'] = $_POST['status'];
    }
} else if(isset($_GET['table-list'])) {
    $controller = new togglecontroller('salespeople');

    echo $controller->getTableList($_GET);
} else {
    $twig->addGlobal('page_title', 'Sales People');
    $twig->addGlobal('session', $_SESSION['FT']);
    $twig->addGlobal('error', $_SESSION['error']);
    $twig->addGlobal('success', $_SESSION['success']);
    unset($_SESSION['error']);
    unset($_SESSION['success']);
    
    // $controller = new togglecontroller('sales');
    // $output = $controller->invoke();
    // $sql = "SELECT * from salespeople where company_id = ".$_SESSION['FT']['company_id']."";
    // $salesman = db_select_assoc_array($sql);
    // $twig->addGlobal('users', $salesman);

    if(!isset($_SESSION['FT']['salespeople_dropdown'])) {
        $_SESSION['FT']['salespeople_dropdown'] = "active";
    }
    $twig->addGlobal('salespeople_dropdown', $_SESSION['FT']['salespeople_dropdown']);
    
    //var_dump($output);
    //var_dump($_SESSION['FT']);
    
    /*
     * Render Templates
     */
    echo $twig->render('administration/tpl.sales.twig');
}