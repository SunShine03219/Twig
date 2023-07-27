<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();
bounce_support();
if (isset($_GET['delete'])) {
	$id = intval($_GET['delete']);
	$affiliate = new affiliate();
	if($affiliate->make_me_into($id)){
		if(!$affiliate->delete_one()){
			$twig->addGlobal('error', 'Unable to delete affiliate.');
		}
	}
	header('Location: /superuser/affiliate');
} else if(strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
    
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
    $affiliate = new affiliate();
    $affiliate->set_company_id($_POST['company_id']);
    $affiliate->set_affiliate($_POST['affiliate']);
    $affiliate->insert_new();
    unset($nonce);
	header('Location: /superuser/affiliate');
}

$twig->addGlobal('page_title', 'Affiliate');
$twig->addGlobal('session', $_SESSION['FT']);

$nonce = new nonce();
$nonce->add('action', 'impersonate');
$nonce->add('user_id', $_SESSION['FT']['user_id']);
$nonce->save();

$companies = company::get_active_companies();
$affiliates = affiliate::get_affiliates();
$affiliated = array();
foreach($affiliates as $el) {
	foreach($companies as $row) {
		if($el['affiliate'] == $row['id']) {
			$row['company_id'] = $el['company_id'];
			$row['affiliate_id'] = $el['id'];
			$affiliated[]= $row;
		}
	}
}
// var_dump($affiliated);die();
usort($affiliated, function($a, $b){
	return $a['company_id'] <=> $b['company_id'];
});

$twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());
$twig->addGlobal('companies', $companies);
$twig->addGlobal('affiliates', $affiliated);

/*
 * Render Templates
 */
echo $twig->render('superuser/tpl.affiliate.twig');