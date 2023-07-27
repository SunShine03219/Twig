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
    
    if (isset($_GET['id'])) {
        if (isset($_GET['action']) && $_GET['action'] == 'toggle') {
            $misc = new misc();
            $id = $_GET['id'];
            if ($misc->make_me_into($id)) {
                if ($misc->get_company_id() != $_SESSION['FT']['company_id']) {
                    $twig->addGlobal('error', 'You are not permitted to change this misc provider.');
                } else {
                    $misc->toggle();
                    if (!$misc->update_one()) {
                        $twig->addGlobal('error', 'Unable to change misc provider status.');
                    } else {
                        header('Location: /admin/misc');
                    }
                }
            }
        } else {
            $twig->addGlobal('page_title', 'Edit Misc Finance Product');
            $twig->addGlobal('session', $_SESSION['FT']);
            
            $misc = new misc();
            $id = $_GET['id'];
            if($misc->make_me_into($id)) {
                $nonce = new nonce();
                $nonce->add('action', 'update');
                $nonce->add('id', $_GET['id']);
                $nonce->add('company_id', $_SESSION['FT']['company_id']);
                $nonce->save();
                
                $twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());
                $twig->addGlobal('miscProvider', $misc->to_array());
            } else {
                $twig->addGlobal('error', 'Misc provider could not be located.');
            }
            
            /*
             * Render Templates
             */
            echo $twig->render('administration/tpl.misc-provider.twig');
        }
    } else if (isset($_GET['action']) && $_GET['action'] == 'new') {
        $twig->addGlobal('page_title', 'New Misc Finance Product');
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
        echo $twig->render('administration/tpl.misc-provider.twig');
    } else if(isset($_GET['dropdown'])) {
        if(isset($_POST['status']) && $_POST['status'] == 'all' || $_POST['status'] == 'active' || $_POST['status'] == 'inactive') {
            $_SESSION['FT']['misc_dropdown'] = $_POST['status'];
        }
    } else if(isset($_GET['table-list'])) {
        $controller = new togglecontroller('misc');
    
        echo $controller->getTableList($_GET);
    } else if(isset($_POST["action"]) && $_POST["action"] == "insert") {
        $misc = new misc();

        if($misc->validate_new($_POST)) {
            if($misc->insert_new()) {
                header("Location: /admin/misc");
            } else {
                header("Location: /admin/misc");
            }
        }
        // var_dump($_POST);
    } else if(isset($_POST["action"]) && $_POST["action"] == "update") {
        $misc = new misc();
        $misc->make_me_into($_POST["id"]);
        if($misc->validate_edit($_POST)) {
            if($misc->update_one()) {
                header("Location: /admin/misc");
            } else {
                header("Location: /admin/misc");
            }
        }
    } else {
        $twig->addGlobal('page_title', 'Misc Finance Product');
        $twig->addGlobal('session', $_SESSION['FT']);
        
        // $controller = new togglecontroller('misc');
        // $output = $controller->invoke();
        // $twig->addGlobal('providers', $output);

        if(!isset($_SESSION['FT']['misc_dropdown'])) {
            $_SESSION['FT']['misc_dropdown'] = "active";
        }
        $twig->addGlobal('misc_dropdown', $_SESSION['FT']['misc_dropdown']);
        
        
        //var_dump($output);
        //var_dump($_SESSION['FT']);
        
        /*
         * Render Templates
         */
        echo $twig->render('administration/tpl.misc.twig');
    }