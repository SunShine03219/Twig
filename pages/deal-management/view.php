<?php
error_reporting(E_ERROR | E_PARSE);
ini_set("xdebug.var_display_max_data", '2000');
/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();

$_SESSION['FT']['current_page'] = 'view';
$twig->addGlobal('session', $_SESSION['FT']);

if ($_POST['action'] == 'edit') {
    // var_dump($_POST);
    // exit;
    $controller = new dealcontroller();
    $output = $controller->invoke('edit');
    // var_dump($output);
} else if (isset($_GET['id'])) {
    $twig->addGlobal('page_title', 'Edit Deal');

    $controller = new dealcontroller();
    $output = $controller->invoke('edit');

    if($output['deal']['locked'] > 0) {
        $twig->addGlobal('disabled', 'disabled');
    }

    $twig->addGlobal('deal', $output['deal']);
    $twig->addGlobal('desk_managers', $output['desk_managers']);
    $twig->addGlobal('finance', $output['finance']);
    $twig->addGlobal('flooring', $output['flooring']);
    $twig->addGlobal('gap', $output['gap']);
    $twig->addGlobal('misc', $output['misc']);
    $twig->addGlobal('leads', $output['leads']);
    $twig->addGlobal('lenders', $output['lenders']);
    $twig->addGlobal('sales', $output['sales']);
    $twig->addGlobal('sales_people', $output['deal']['salespeople']);
    $twig->addGlobal('warranty', $output['warranty']);
    $twig->addGlobal('notes', $output['notes']);
    $twig->addGlobal('sec_deals_permission', $_SESSION['FT']['SEC_DEALS_PERMISSION']);
    $twig->addGlobal('hide_customize_btn', 'yes');


    $misc_id = $output['misc'];

    $custom_var = $output['deal']['finance_sale'];
    $custom_var1= $output['deal']['finance_cost'];
    $misc_id= $output['deal']['misc_id'];
    $json_misc_id = json_decode($misc_id);
    $deal_finance_sale_val = json_decode($custom_var);
    $deal_finance_cost_val = json_decode($custom_var1);
    $existing_directory = array();

    foreach($deal_finance_sale_val as $index => $value) {
        $existing_directory1 = array();
        $existing_directory1[] = $json_misc_id[$index];
        $existing_directory1[] = $deal_finance_sale_val[$index]; 
        $existing_directory1[] = $deal_finance_cost_val[$index];
       array_push($existing_directory, $existing_directory1);
     }
     
    $twig->addGlobal('deal_finance_sale', $existing_directory);
    


    $twig->addGlobal('misc_finance', $misc_id);



    $temp = $output['deal']['pickuppayments'];
    if ($output['deal']['pickuppayments']) {
        $pickupPaymentArray = [];
        $pickUpPayments = new pickuppayment;
        $pickUpReport = new pickuppaymentreport;
        foreach($output['deal']['pickuppayments'] as $paymentId) {
            if ($pickUpPayments->make_me_into($paymentId)) {
                $payment = $pickUpPayments->to_array();
                $payment['date_due_formatted'] = date_format(date_create($payment['date_due']), "m/d/Y");
                $payment['status'] = $pickUpReport->get_payment_status($payment);
                array_push($pickupPaymentArray, $payment);
            }
        }
        //var_dump($pickupPaymentArray);
        $twig->addGlobal('pickup_payments', $pickupPaymentArray);
    }
    
    $nonce = new nonce();
    $nonce->add('action', 'edit');
    $nonce->add('company_id', $_SESSION['FT']['company_id']);
    $nonce->save();
    
    $twig->addGlobal('nonce_list', $nonce->get_nonce_list());
    
    $setting = new dealsetting();
    $settinglist = new dealsettinglist();
    $settinglist->load_company_id($_SESSION['FT']['company_id']);
    $twig->addGlobal('doc_fee_section', $settinglist->__get('doc_fee'));
    $twig->addGlobal('pack_section', $settinglist->__get('pack'));
    //var_dump($pickupPaymentArray);
    $twig->addGlobal('pickup_payments', $pickupPaymentArray);
    
    //var_dump($output);
    //var_dump($_SESSION['FT']);
    
    /*
     * Render Templates
     */
    echo $twig->render('forms/tpl.deal.twig');
    
} else if(isset($_GET['dropdown'])) {
    $_SESSION['FT']['topbar_filters'] = $_POST;
} else if(isset($_GET['table-list'])) {
    $controller = new dealcontroller();

    echo $controller->getTableList($_GET, $_SESSION['FT']['company_id']);
} else {
    $twig->addGlobal('page_title', 'View Deals');
    $twig->addGlobal('session', $_SESSION['FT']);
    
    // $controller = new dealcontroller();
    // $output = $controller->invoke('view');


    // if(isset($_GET['type'])){
    //     if($_GET['type'] == "pagination" || $_GET['type'] == "sort"){
    //         echo json_encode($output);
    //         exit();
    //     }
    // }
    
    // $twig->addGlobal('deals', $output);

    if(!isset($_SESSION['FT']['deals_dropdown'])) {
        $_SESSION['FT']['deals_dropdown'] = "all";
    }
    $_SESSION['current_page'] = 'view';
    $_SESSION['FT']['query_string'] = $_SERVER['QUERY_STRING'];
    $twig->addGlobal('deals_dropdown', $_SESSION['FT']['deals_dropdown']);
    
    //var_dump($output);
    //var_dump($_SESSION['FT']);
    
    
    /*
     * Render Templates
     */
    echo $twig->render('deal-management/tpl.view.twig');
}