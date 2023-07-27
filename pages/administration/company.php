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

$twig->addGlobal('page_title', 'Edit Customer');
$twig->addGlobal('session', $_SESSION['FT']);
$twig->addGlobal('form_parent', 'administration');

if (isset($_POST['id'])) {
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
                	$_SESSION['FT']['message'] = 'Company information is updated';
                    header('Location: /admin/company');
                }
            }
        } else {
            $twig->addGlobal('error', 'Unable to retrieve company ' . $id);
        }
    }
    unset($nonce);
} else {
    $id = $_SESSION['FT']['company_id'];
    $company = new company();
    if($company->make_me_into($id)){
        
        $nonce = new nonce();
        $nonce->add('action', 'update');
        $nonce->add('id', $id);
        $nonce->save();

        if($_SESSION['FT']['message'] != ''){
        	 $twig->addGlobal('success', $_SESSION['FT']['message']);
        }
        $_SESSION['FT']['message'] = '';
        $twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());
        $twig->addGlobal('company', $company->to_array());
        
    } else {
        $twig->addGlobal('error', 'Unable to retrieve company ' . $id);
    }
}

/*
 * Render Templates
 */
echo $twig->render('administration/tpl.company.twig');