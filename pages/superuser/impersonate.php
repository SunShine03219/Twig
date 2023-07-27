<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});
if(urldecode($_GET['id'] != $_SESSION['user_id']) && $_POST['id'] != $_SESSION['user_id']){
	bounce();
	bounce_support();
}
if(strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
    
    $nonce = new nonce($_POST['nonce']);
    $result = $nonce->test_request($_POST);
    if($result){
        if($_SESSION['FT']['user_id'] == intval($_POST['user_id'])){
            $company_id = intval($_POST['company_id']);
            if(!empty($company_id)){
                $_SESSION['FT']['company_id'] = $company_id;
            }
        }
    }
    $company = new company();
    $company->make_me_into(intval($_POST['company_id']));
    $company->set_username("{$_SESSION['FT']['name_first']} {$_SESSION['FT']['name_last']}");
    $company->set_last_login(date("y-m-d h:i:s"));
    $company->update_one();
    unset($nonce);

    $twig->addGlobal('company_switch', true);
    $twig->addGlobal('company_name', company::company_name($_POST['company_id']));
}

$twig->addGlobal('page_title', 'Impersonate');
$twig->addGlobal('session', $_SESSION['FT']);

$nonce = new nonce();
$nonce->add('action', 'impersonate');
$nonce->add('user_id', $_SESSION['FT']['user_id']);
$nonce->save();

$twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());
$twig->addGlobal('companies', company::get_active_companies());

/*
 * Render Templates
 */
echo $twig->render('superuser/tpl.impersonate.twig');