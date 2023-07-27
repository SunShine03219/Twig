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
            $toggle->set_table('deskmanagers');
            if ($toggle->make_me_into($id)) {
                if (!$toggle->validate_edit($_POST)) {
                    $twig->addGlobal('error', 'Desk Manager already exist with the same name.');
                    $nonce = new nonce();
                    renderEditFormView($nonce, $twig, $_SESSION, $id);
                } else {
                    if (!$toggle->update_one()) {
                        $_SESSION['error'] = 'Unable to update Desk Manager.';
                    } else {
                        $_SESSION['success'] = 'Desk Manager is updated successfully.';
                        //header('Location: /admin/sales');
                    }
                }
            } else {
                $_SESSION['error'] = 'Unable to retrieve Desk Manager ' . $id;
            }
            header('Location: /admin/desk');
            //loadDeskManagers($twig, $_SESSION);
        }
    }
} else if (isset($_POST['action']) && $_POST['action'] == 'insert') {
    $nonce = new nonce($_POST['nonce']);
    $result = $nonce->test_request($_POST);
    if($result){
        $toggle = new toggle();
        $toggle->set_table('deskmanagers');
        if (!$toggle->validate_new($_POST)) {
            $twig->addGlobal('error', 'Name already exist, Please Add a different one.');
            //header('Location: /admin/desk');
            //echo $twig->render('administration/tpl.sales.twig');
            $nonce = new nonce();
            renderFormView($nonce, $twig, $_SESSION);
        } else {
            if (!$toggle->insert_new()) {
                $_SESSION['error'] = 'Unable to add DeskManager.';
            } else {
                $_SESSION['success'] = 'Desk Manager added successfully.';
                //header('Location: /admin/desk');
            }
        }
        header('Location: /admin/desk');
        //loadDeskManagers($twig, $_SESSION);
    }
    
} else if (isset($_GET['id'])) {
    if (isset($_GET['action']) && $_GET['action'] == 'toggle') {
        $desk = new deskmanager();
        $id = $_GET['id'];
        if ($desk->make_me_into($id)) {
            if ($desk->get_company_id() != $_SESSION['FT']['company_id']) {
                $twig->addGlobal('error', 'You are not permitted to change this user.');
            } else {
                $desk->toggle();
                if (!$desk->update_one()) {
                    $twig->addGlobal('error', 'Unable to change desk manager status.');
                } else {
                    header('Location: /admin/desk');
                }
            }
        }
    } else {
        $nonce = new nonce();
        renderEditFormView($nonce, $twig, $_SESSION, $_GET['id']);
    }
} else if (isset($_GET['action']) && $_GET['action'] == 'new') {
    $nonce = new nonce();
    renderFormView($nonce, $twig, $_SESSION);
} else if(isset($_GET['dropdown'])) {
    if(isset($_POST['status']) && $_POST['status'] == 'all' || $_POST['status'] == 'active' || $_POST['status'] == 'inactive') {
        $_SESSION['FT']['deskmanager_dropdown'] = $_POST['status'];
    }
} else if(isset($_GET['table-list'])) {
    $controller = new togglecontroller('deskmanager');

    echo $controller->getTableList($_GET);
} else {
    if(!isset($_SESSION['FT']['deskmanager_dropdown'])) {
        $_SESSION['FT']['deskmanager_dropdown'] = "active";
    }
    $twig->addGlobal('deskmanager_dropdown', $_SESSION['FT']['deskmanager_dropdown']);
    $twig->addGlobal('error', $_SESSION['error']);
    $twig->addGlobal('success', $_SESSION['success']);
    unset($_SESSION['error']);
    unset($_SESSION['success']);
    //echo $_SESSION['success'];exit;
    loadDeskManagers($twig, $_SESSION);
}

function renderFormView($nonce, $twig, $session) {
    $twig->addGlobal('page_title', 'New Desk Manager');
    $twig->addGlobal('session', $session['FT']);
    $twig->addGlobal('action', 'insert');

    $nonce->add('action', 'insert');
    $nonce->add('company_id', $session['FT']['company_id']);
    $nonce->save();

    $twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());
    
    /*
     * Render Templates
     */
    echo $twig->render('administration/tpl.desk-manager.twig');
} 

function renderEditFormView($nonce, $twig, $session, $id) {
    $twig->addGlobal('page_title', 'Edit Desk Managers');
        $twig->addGlobal('session', $_SESSION['FT']);

        $deskmanager = new deskmanager();
        if($deskmanager->make_me_into($id)) {
            $nonce = new nonce();
            $nonce->add('action', 'update');
            $nonce->add('id', $id);
            $nonce->add('company_id', $_SESSION['FT']['company_id']);
            $nonce->save();

            $twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());
            $twig->addGlobal('deskManager', $deskmanager->to_array());
        } else {
            $twig->addGlobal('error', 'Desk Manager could not be located.');
        }

        /*
         * Render Templates
         */
        echo $twig->render('administration/tpl.desk-manager.twig');
}

function loadDeskManagers($twig, $session) {
    $twig->addGlobal('page_title', 'Desk Managers');
    $twig->addGlobal('session', $session['FT']);

    $sql = "SELECT * from deskmanagers where company_id = ".$session['FT']['company_id']."";
    $output = db_select_assoc_array($sql);
    $twig->addGlobal('users', $output);
    
    /*
     * Render Templates
     */
    echo $twig->render('administration/tpl.desk.twig');
}

