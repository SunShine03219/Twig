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
            $toggle->set_table('warrantyproviders');
            if ($toggle->make_me_into($id)) {
                if (!$toggle->validate_edit($_POST)) {
                    $_SESSION['error'] = 'Warranty Provider already exist with the same name.';
                } else {
                    if (!$toggle->update_one()) {
                        $_SESSION['error'] = 'Unable to update Warranty Provider.';
                    } else {
                        $_SESSION['success'] = 'Warranty Provider is updated successfully.';
                        //header('Location: /admin/sales');
                    }
                }
            } else {
                $_SESSION['error'] = 'Unable to retrieve user ' . $id;
            }
            header('Location: /admin/warranty');

            // $twig->addGlobal('page_title', 'Warranty Providers');
            // $twig->addGlobal('session', $_SESSION['FT']);
            // // $sql = "SELECT * from warrantyproviders where company_id = ".$_SESSION['FT']['company_id']." AND active=1";
            // $sql = "SELECT id, name, active, IF(active>0, 'Active', 'Inactive') as status from warrantyproviders where company_id = ".$_SESSION['FT']['company_id']." AND active=1 ORDER BY name ASC";
            // $output = db_select_assoc_array($sql);
            // $twig->addGlobal('providers', $output);

            // if(!isset($_SESSION['FT']['warrantyproviders_dropdown'])) {
            //     $_SESSION['FT']['warrantyproviders_dropdown'] = "active";
            // }
            // $twig->addGlobal('warrantyproviders_dropdown', $_SESSION['FT']['warrantyproviders_dropdown']);
            // echo $twig->render('administration/tpl.warranty.twig');
        }
    }
} else if (isset($_POST['action']) && $_POST['action'] == 'insert') {
    $nonce = new nonce($_POST['nonce']);
    $result = $nonce->test_request($_POST);
    if($result){
        $toggle = new toggle();
        $toggle->set_table('warrantyproviders');
        if (!$toggle->validate_new($_POST)) {
            $_SESSION['error'] = 'Name already exist, Please enter a valid name.';
            //header('Location: /admin/sales');
            //echo $twig->render('administration/tpl.sales.twig');
        } else {
            if (!$toggle->insert_new()) {
                $_SESSION['error'] = 'Unable to add Warranty Provider.';
            } else {
                $_SESSION['success'] = 'Warranty Provider added successfully.';
                //header('Location: /admin/sales');
            }
        }

        header('Location: /admin/warranty');

        // $twig->addGlobal('page_title', 'Warranty Provider');
        // $twig->addGlobal('session', $_SESSION['FT']);

        // if(!isset($_SESSION['FT']['warrantyproviders_dropdown'])) {
        //     $_SESSION['FT']['warrantyproviders_dropdown'] = "active";
        // }
        // // $sql = "SELECT * from warrantyproviders where company_id = ".$_SESSION['FT']['company_id']." AND active=1";
        // $sql = "SELECT id, name, active, IF(active>0, 'Active', 'Inactive') as status from warrantyproviders where company_id = ".$_SESSION['FT']['company_id']." AND active=1 ORDER BY name ASC";
        // $output = db_select_assoc_array($sql);
        // $twig->addGlobal('providers', $output);
        // $twig->addGlobal('warrantyproviders_dropdown', $_SESSION['FT']['warrantyproviders_dropdown']);
        // echo $twig->render('administration/tpl.warranty.twig');
    }
    
} else if (isset($_GET['id'])) {
    if (isset($_GET['action']) && $_GET['action'] == 'toggle') {
        $warranty = new warranty();
        $id = $_GET['id'];
        if ($warranty->make_me_into($id)) {
            if ($warranty->get_company_id() != $_SESSION['FT']['company_id']) {
                $twig->addGlobal('error', 'You are not permitted to change this warranty provider.');
            } else {
                $warranty->toggle();
                if (!$warranty->update_one()) {
                    $twig->addGlobal('error', 'Unable to change warranty provider status.');
                } else {
                    header('Location: /admin/warranty');
                }
            }
        }
    } else {
        $twig->addGlobal('page_title', 'Edit Warranty Provider');
        $twig->addGlobal('session', $_SESSION['FT']);

        $warranty = new warranty();
        $id = $_GET['id'];
        if($warranty->make_me_into($id)) {
            $nonce = new nonce();
            $nonce->add('action', 'update');
            $nonce->add('id', $_GET['id']);
            $nonce->add('company_id', $_SESSION['FT']['company_id']);
            $nonce->save();
            
            $twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());
            $twig->addGlobal('warrantyProvider', $warranty->to_array());
        } else {
            $twig->addGlobal('error', 'Warranty provider could not be located.');
        }

        /*
         * Render Templates
         */
        echo $twig->render('administration/tpl.warranty-provider.twig');
    }
} else if (isset($_GET['action']) && $_GET['action'] == 'new') {
    $twig->addGlobal('page_title', 'New Warranty Provider');
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
    echo $twig->render('administration/tpl.warranty-provider.twig');
} else if(isset($_GET['dropdown'])) {
    if(isset($_POST['status']) && $_POST['status'] == 'all' || $_POST['status'] == 'active' || $_POST['status'] == 'inactive') {
        $_SESSION['FT']['warranty_dropdown'] = $_POST['status'];
    }
} else if(isset($_GET['table-list'])) {
    $controller = new togglecontroller('warranty');

    echo $controller->getTableList($_GET);
} else {
    $twig->addGlobal('page_title', 'Warranty Providers');
    $twig->addGlobal('session', $_SESSION['FT']);
    $twig->addGlobal('error', $_SESSION['error']);
    $twig->addGlobal('success', $_SESSION['success']);
    unset($_SESSION['error']);
    unset($_SESSION['success']);
    
    $controller = new togglecontroller('warranty');
    $output = $controller->invoke();
    $twig->addGlobal('providers', $output);
    $twig->addGlobal('warranty_dropdown', $_SESSION['FT']['warranty_dropdown']);
    
    //var_dump($output);
    //var_dump($_SESSION['FT']);
    
    /*
     * Render Templates
     */
    echo $twig->render('administration/tpl.warranty.twig');
}