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
            $toggle->set_table('financepeople');
            if ($toggle->make_me_into($id)) {
                if (!$toggle->validate_edit($_POST)) {
                    $_SESSION['error'] = 'Finance Person already exist with the same name.';
                } else {
                    if (!$toggle->update_one()) {
                        $_SESSION['error'] = 'Unable to update Finance Person.';
                    } else {
                        $_SESSION['success'] = 'Finance Person is updated successfully.';
                        //header('Location: /admin/sales');
                    }
                }
            } else {
                $_SESSION['error'] = 'Unable to retrieve user ' . $id;
            }
            // $twig->addGlobal('page_title', 'Finance People');
            // $twig->addGlobal('session', $_SESSION['FT']);
            // // $sql = "SELECT * from financepeople where company_id = ".$_SESSION['FT']['company_id']." AND active=1";
            // $sql = "SELECT id, name, active, IF(active>0, 'Active', 'Inactive') as status from financepeople where company_id = ".$_SESSION['FT']['company_id']." AND active=1 ORDER BY name ASC";
            // $output = db_select_assoc_array($sql);
            // $twig->addGlobal('users', $output);

            // if(!isset($_SESSION['FT']['financepeople_dropdown'])) {
            //     $_SESSION['FT']['financepeople_dropdown'] = "active";
            // }
            // $twig->addGlobal('financepeople_dropdown', $_SESSION['FT']['financepeople_dropdown']);
            // echo $twig->render('administration/tpl.finance.twig');
        }
        header('Location: /admin/finance');
    }
} else if (isset($_POST['action']) && $_POST['action'] == 'insert') {
    $nonce = new nonce($_POST['nonce']);
    $result = $nonce->test_request($_POST);
    if($result){
        $toggle = new toggle();
        $toggle->set_table('financepeople');
        if (!$toggle->validate_new($_POST)) {
            //$twig->addGlobal('error', 'Name already exist, Please Add a different one.');
            $_SESSION['error'] = 'Name already exist, Please Add a different one.';
            //header('Location: /admin/sales');
            //echo $twig->render('administration/tpl.sales.twig');
        } else {
            if (!$toggle->insert_new()) {
                $_SESSION['error'] = 'Unable to add Finance Person.';
            } else {
                $_SESSION['success'] = 'Finance Person added successfully.';
                //header('Location: /admin/sales');
            }
        }
        // $twig->addGlobal('page_title', 'Finance People');
        // $twig->addGlobal('session', $_SESSION['FT']);

        // if(!isset($_SESSION['FT']['financepeople_dropdown'])) {
        //     $_SESSION['FT']['financepeople_dropdown'] = "active";
        // }
        // // $sql = "SELECT * from financepeople where company_id = ".$_SESSION['FT']['company_id']." AND active=1";
        // $sql = "SELECT id, name, active, IF(active>0, 'Active', 'Inactive') as status from financepeople where company_id = ".$_SESSION['FT']['company_id']." AND active=1 ORDER BY name ASC";
        // $output = db_select_assoc_array($sql);
        // $twig->addGlobal('users', $output);
        // $twig->addGlobal('financepeople_dropdown', $_SESSION['FT']['financepeople_dropdown']);
        // echo $twig->render('administration/tpl.finance.twig');
    }
    header('Location: /admin/finance');
    
} else if (isset($_GET['id'])) {
    if (isset($_GET['action']) && $_GET['action'] == 'toggle') {
        $finance = new finance();
        $id = $_GET['id'];
        if ($finance->make_me_into($id)) {
            if ($finance->get_company_id() != $_SESSION['FT']['company_id']) {
                $twig->addGlobal('error', 'You are not permitted to change this user.');
            } else {
                $finance->toggle();
                if (!$finance->update_one()) {
                    $twig->addGlobal('error', 'Unable to change finance person status.');
                } else {
                    header('Location: /admin/finance');
                }
            }
        }
    } else {
        $twig->addGlobal('page_title', 'Edit Finance People');
        $twig->addGlobal('session', $_SESSION['FT']);

        $finance = new finance();
        $id = $_GET['id'];
        if($finance->make_me_into($id)) {
            $nonce = new nonce();
            $nonce->add('action', 'update');
            $nonce->add('id', $_GET['id']);
            $nonce->add('company_id', $_SESSION['FT']['company_id']);
            $nonce->save();

            $twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());
            $twig->addGlobal('financePerson', $finance->to_array());
        } else {
            $twig->addGlobal('error', 'Finance person could not be located.');
        }

        /*
         * Render Templates
         */
        echo $twig->render('administration/tpl.finance-person.twig');
    }
} else if (isset($_GET['action']) && $_GET['action'] == 'new') {
    $twig->addGlobal('page_title', 'New Finace Person');
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
    echo $twig->render('administration/tpl.finance-person.twig');
} else if(isset($_GET['dropdown'])) {
    if(isset($_POST['status']) && $_POST['status'] == 'all' || $_POST['status'] == 'active' || $_POST['status'] == 'inactive') {
        $_SESSION['FT']['finance_dropdown'] = $_POST['status'];
    }
} else if(isset($_GET['table-list'])) {
    $controller = new togglecontroller('finance');

    echo $controller->getTableList($_GET);
} else {
    $twig->addGlobal('page_title', 'Finance People');
    $twig->addGlobal('session', $_SESSION['FT']);
    $twig->addGlobal('error', $_SESSION['error']);
    $twig->addGlobal('success', $_SESSION['success']);
    unset($_SESSION['error']);
    unset($_SESSION['success']);
    
    // $controller = new togglecontroller('finance');
    // $output = $controller->invoke();
    // $twig->addGlobal('users', $output);
    
    if(!isset($_SESSION['FT']['finance_dropdown'])) {
        $_SESSION['FT']['finance_dropdown'] = "active";
    }
    $twig->addGlobal('finance_dropdown', $_SESSION['FT']['finance_dropdown']);
    /*
     * Render Templates
     */
    echo $twig->render('administration/tpl.finance.twig');
}