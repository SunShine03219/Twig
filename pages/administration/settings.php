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

$twig->addGlobal('page_title', 'Company Settings');
$twig->addGlobal('session', $_SESSION['FT']);

$setting = new dealsetting();
$settinglist = new dealsettinglist();

if(isset($_POST['action']) && $_POST['action'] == "save"){
    $data['name'] = "pack";
    if($_POST['acvoption'] == "packinacv"){
        $data['setting'] = 0;
    }else{
        $data['setting'] = 1;
    }
    $data['value'] = $_POST['defaultpack'];
    $data['required'] = 0;
    $setting_data[0] = $data;

    $data['name'] = "doc_fee";
    if($_POST['trackdoc'] == "dotrack"){
        $data['setting'] = 0;
    }else{
        $data['setting'] = 1;
    }
    $data['value'] = $_POST['defaultdocfee'];
    $setting_data[1] = $data;

    $data['name'] = "flooringsection";
    $data['setting'] = $_POST['flooring'];
    $data['value'] = 0;
    $setting_data[2] = $data;

    $data['name'] = "pickuppayments";
    $data['setting'] = $_POST['pickups'];
    $data['value'] = 0;
    $setting_data[3] = $data;

    $settinglist->load($setting_data);
    $settinglist->save_company_id($_POST['company_id']);
    $twig->addGlobal('success', 'Settings are updated successfully.');
}

if((isset($_SESSION['FT']['SEC_SUPPORT']) && $_SESSION['FT']['SEC_SUPPORT']==1) || (isset($_SESSION['FT']['SEC_ADMIN']) && $_SESSION['FT']['SEC_ADMIN']==1)){
    $settinglist->load_company_id($_SESSION['FT']['company_id']);

    $twig->addGlobal('flooring_section', $settinglist->__get('flooringsection'));
    $twig->addGlobal('doc_fee_section', $settinglist->__get('doc_fee'));
    $twig->addGlobal('pack_section', $settinglist->__get('pack'));
    $twig->addGlobal('pickuppayments_section', $settinglist->__get('pickuppayments'));

    $nonce = new nonce();
    $nonce->add('action', 'update');
    $nonce->add('company_id', $_SESSION['FT']['company_id']);
    $nonce->save();

    $twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());
}

//var_dump($output);
//var_dump($_SESSION['FT']);

/*
 * Render Templates
 */
echo $twig->render('administration/tpl.settings.twig');