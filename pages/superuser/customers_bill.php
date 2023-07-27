<?php
    //error_reporting(E_ERROR | E_PARSE);
    error_reporting(E_ALL);

    /*
     * Autoload Queue
     */
    spl_autoload_register(function ($class) {
        include '_includes/classes/' . $class . '_class.php';
    });

    bounce();
    bounce_support();

    if (isset($_GET['id']) || isset($_POST['id'])) {

        $twig->addGlobal('page_title', 'Edit Customer');
        $twig->addGlobal('session', $_SESSION['FT']);

        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $company = new company();
            if($company->make_me_into($id)){

                $nonce = new nonce();
                $nonce->add('action', 'update');
                $nonce->add('id', $_GET['id']);
                $nonce->save();

                $twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());
                $twig->addGlobal('company', $company->to_array());

            } else {
                $twig->addGlobal('error', 'Unable to retrieve company ' . $id);
            }

        } else {
            $nonce = new nonce($_POST['nonce']);
            $result = $nonce->test_request($_POST);
            if($result) {
                $id = intval($_POST['id']);
                $company = new company();
                if($company->make_me_into($id)){
                    if(!$company->validate_edit($_POST)){
                        $twig->addGlobal('error', 'Unable to validate form data.');
                    } else {
                        if(!$company->update_one()){
                            $twig->addGlobal('error', 'Unable to update company.');
                        } else {
                            header('Location: /superuser/customers');
                        }
                    }
                } else {
                    $twig->addGlobal('error', 'Unable to retrieve company ' . $id);
                }
            }
            unset($nonce);
        }

        /*
         * Render Templates
         */
        echo $twig->render('superuser/tpl.customer.twig');
    } else if (isset($_GET['action']) && $_GET['action'] == 'new') {
        $twig->addGlobal('page_title', 'New Customer');
        $twig->addGlobal('session', $_SESSION['FT']);
        $twig->addGlobal('action', 'insert');
        $twig->addGlobal('error', $_SESSION['error']);
        unset($_SESSION['error']);
        
        $nonce = new nonce();
        $nonce->add('action', 'insert');
        $nonce->save();

        $twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());

        /*
         * Render Templates
         */
        echo $twig->render('superuser/tpl.customer.twig');
    } else if (isset($_POST['action']) && $_POST['action'] == 'insert') {

        $company = new company();

        $nonce = new nonce($_POST['nonce']);

        $result = $nonce->test_request($_POST);

        if ($result) {
            if(!$company->validate_new($_POST)) {
                $twig->addGlobal('error', 'Company name already in use.');
                $_SESSION['error'] = 'Company name already in use.';
                header('Location: /superuser/customers?action=new');
            } else {
                if(!$company->insert_new()){
                    $twig->addGlobal('error', 'Unable to create customer.');
                } else {
                    header('Location: /superuser/customers');
                }
            }
        }
    } else if(isset($_GET['table-list'])) {
        $controller = new companycontroller();

        echo $controller->getCustomersBillsTableList($_GET);
    } else {
        $twig->addGlobal('page_title', 'Customers');
        $twig->addGlobal('session', $_SESSION['FT']);
        if(isset($_GET['name']))
        $twig->addGlobal('search_company', $_GET['name']);

        // $controller = new companycontroller();
        // $output = $controller->invoke();
        // $twig->addGlobal('customers', $output);
        // if(isset($_GET['type'])){
        //     if($_GET['type'] == "pagination" || $_GET['type'] == "sort"){
        //         echo json_encode($output);
        //         exit();
        //     }
        // }
        /*
         * Render Templates
         */
        echo $twig->render('superuser/tpl.customers_bill.twig');
    }