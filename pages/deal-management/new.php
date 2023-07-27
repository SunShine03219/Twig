<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();

if ($_POST['action'] == 'new') {
    $_SESSION['new_deal_info'] = $_POST;
    $controller = new dealcontroller();
    $output = $controller->invoke('new');
} else {
    if ($_POST['type'] == 'getNote') {
        $id = $_POST['id'];
		$result = db_select_assoc_array("select dn.*, from_unixtime(dn.posted,'%Y-%m-%d %h:%i:%s') as note_date,u.name_first, u.name_last from `dealnotes` as dn inner join users as u on dn.user_id = u.id where deal_id='$id'");
        foreach($result as &$item){
            $item['posted'];
        }
        echo json_encode($result); die();
    }

    if ($_POST['type'] == 'deleteDeal') {
        $userId = $_SESSION['FT']['user_id'];
        $dateTimeStamp = date('Y-m-d h:i:s');
        $id = $_POST['id'];
        if($_SESSION['FT']['SEC_DELETE'] > 0) {
    		$sql = "UPDATE deals SET deleted = 1, closed_dms = 1, deleted_by = ".$userId.", deleted_timestamp = '".$dateTimeStamp."' WHERE id='" . $id . "'";
    		//db_delete_bare($sql);//yes, terrible, I know
            if(db_delete_bare($sql)) {
                // $sql = "DELETE from dealnotes WHERE id='" . $id . "'";
                // db_delete_bare($sql);//yes, terrible, I know

                // $sql = "DELETE from deals_to_sales WHERE id='" . $id . "'";
                // db_delete_bare($sql);//yes, terrible, I know
                
                echo json_encode("success"); exit();
            }else{
                echo json_encode("failed"); exit(); 
            }
    		
        } else {
            echo json_encode(403); exit();
        }
    }

    if ($_POST['type'] == 'lockUnlockDeal') {
        $id = $_POST['id'];
        if($_POST['setting'] == "unlock") {
            $lock = 0;
            $note_content = "Deal Unlocked";
        } else {
            $lock = 1;
            $note_content = "Deal Locked";
        }
        if($_SESSION['FT']['SEC_DEALS_PERMISSION'] > 0) {
            $sql = "UPDATE deals SET locked = {$lock} WHERE id='" . $id . "'";
            if(db_update_bare($sql)) {
                $date = new DateTime();
                $posted = $date->getTimestamp();
                $sqlNote = "insert into dealnotes set deal_id = ".$id.
                ", user_id = ".$_SESSION['FT']['user_id'].
                ", posted = ". $posted .
                ", note = '".$note_content ."'";
                db_insert_bare($sqlNote);
                echo json_encode("success"); exit();
            } else {
                echo json_encode("failed"); exit(); 
            }
        } else {
            echo json_encode(403); exit();
        }
        
    }

    $desk_controller = new deskmanager();
    $sales_controller = new sales();
    $flooring_controller = new flooring();
    $lead_controller = new lead();
    $finance_controller = new finance();
    $lender_controller = new lender();
    $warranty_controller = new warranty();
    $gap_controller = new gap();
    $misc_controller = new misc();
    $pickuppayment = new pickuppayment();
    
    if($_POST['action'] == "insert"){

        if($_POST['type'] == "add_new_note"){
            $note_id = $_POST['note_id'];
            $note_content = $_POST['note_content'];

            $date = new DateTime();
            $posted = $date->getTimestamp();
            $sql = "insert into dealnotes set deal_id = ".$note_id.
            ", user_id = ".$_SESSION['FT']['user_id'].
            ", posted = ". $posted .
            ", note = '".$note_content ."'";
            $result = db_insert_bare($sql);
            header("location: /deals/".$_SESSION['current_page']);
        }

        if($_POST["type"] == "addPayment"){

            $insert_data['deal_id'] = $_POST['add_deal_id'];
            $insert_data['amount'] = $_POST['amount'];
            $insert_data['date_due'] = $_POST['due_date'];
            $insert_data['payment_method'] = $_POST['payment_method'];

            $insert_data['paid'] = isset($_POST['paid_check'])?1:0;
            $insert_data['note'] = isset($_POST['pay_note']);
            $insert_data['created_by'] = $_SESSION['FT']['company_id'];
            $pickuppayment->set_deal_id($insert_data['deal_id']);
            $pickuppayment->set_paid($insert_data['paid']);
            $pickuppayment->set_date_paid('0');
            if($pickuppayment->validate_new($insert_data))
            {
                $pickuppayment->insert_new();
            }
            if($insert_data['deal_id'] == 0){
                header("location: /deals/new");
            }else{
                header("location: /deals/view?id=".$insert_data['deal_id']);
            }
        }
        if($_POST["type"] == "deskmanager"){
            $insert_data['name'] = $_POST['name'];
            $insert_data['company_id'] = $_SESSION['FT']['company_id'];
            if($desk_controller->validate_new($insert_data))
            {
                $temp = $desk_controller->insert_new();
            }else{
                echo "fail";
                exit();
            }
            echo $temp;
            exit();
        }
        if($_POST["type"] == "sales"){
            $insert_data['name'] = $_POST['name'];
            $insert_data['company_id'] = $_SESSION['FT']['company_id'];
            if($sales_controller->validate_new($insert_data))
            {
                $sales_controller->insert_new();
            }
            $insert_data['deal_id'] = $_POST['add_deal_id'];
            if($insert_data['deal_id'] == 0){
                header("location: /deals/new");
            }else{
                header("location: /deals/view?id=".$insert_data['deal_id']);
            }    
        }
        if($_POST["type"] == "flooring"){
            $insert_data['name'] = $_POST['name'];
            $insert_data['company_id'] = $_SESSION['FT']['company_id'];
            if($flooring_controller->validate_new($insert_data))
            {
                $flooring_controller->insert_new();
            }else{
                echo "fail";
                exit();
            }
            echo $temp;
            exit(); 
        }
        if($_POST["type"] == "lead"){
            $insert_data['name'] = $_POST['name'];
            $insert_data['company_id'] = $_SESSION['FT']['company_id'];
            if($lead_controller->validate_new($insert_data))
            {
                $lead_controller->insert_new();
            }else{
                echo "fail";
                exit();
            }
            echo $temp;
            exit(); 
        }
        if($_POST["type"] == "finance"){
            $insert_data['name'] = $_POST['name'];
            $insert_data['company_id'] = $_SESSION['FT']['company_id'];
            if($finance_controller->validate_new($insert_data))
            {
                $finance_controller->insert_new();
            }else{
                echo "fail";
                exit();
            }
            echo $temp;
            exit();  
        }
        if($_POST["type"] == "lender"){
            $insert_data['name'] = $_POST['name'];
            $insert_data['company_id'] = $_SESSION['FT']['company_id'];
            if($lender_controller->validate_new($insert_data))
            {
                $lender_controller->insert_new();
            }else{
                echo "fail";
                exit();
            }
            echo $temp;
            exit();  
        }
        if($_POST["type"] == "warranty"){
            $insert_data['name'] = $_POST['name'];
            $insert_data['company_id'] = $_SESSION['FT']['company_id'];
            if($warranty_controller->validate_new($insert_data))
            {
                $warranty_controller->insert_new();
            }else{
                echo "fail";
                exit();
            }
            echo $temp;
            exit();   
        }
        if($_POST["type"] == "gap"){
            $insert_data['name'] = $_POST['name'];
            $insert_data['company_id'] = $_SESSION['FT']['company_id'];
            if($gap_controller->validate_new($insert_data))
            {
                $gap_controller->insert_new();
            }else{
                echo "fail";
                exit();
            }
            echo $temp;
            exit();
        }
        if($_POST["type"] == "misc"){
            $insert_data['name'] = $_POST['name'];
            $insert_data['company_id'] = $_SESSION['FT']['company_id'];
            if($misc_controller->validate_new($insert_data))
            {
                $misc_controller->insert_new();
            }else{
                echo "fail";
                exit();
            }
            echo $temp;
            exit();
        }
    }

    $twig->addGlobal('session', $_SESSION['FT']);
    
    $twig->addGlobal('page_title', 'New Deal');
    $twig->addGlobal('action', 'new');
    
    $desk_managers = $desk_controller->get_active_deskmanagers($_SESSION['FT']['company_id']);
    $twig->addGlobal('desk_managers', $desk_managers);
    
    $salespeople = $sales_controller->get_active_salespeople($_SESSION['FT']['company_id']);
    $twig->addGlobal('sales', $salespeople);
    
    $flooring = $flooring_controller->get_flooring_companies($_SESSION['FT']['company_id']);
    $twig->addGlobal('flooring', $flooring);
    
    $leads = $lead_controller->get_lead_companies($_SESSION['FT']['company_id']);
    $twig->addGlobal('leads', $leads);
    
    $finance = $finance_controller->get_active_financepeople($_SESSION['FT']['company_id']);
    $twig->addGlobal('finance', $finance);
    
    $lenders = $lender_controller->get_active_lenders($_SESSION['FT']['company_id']);
    $twig->addGlobal('lenders', $lenders);
    
    $gap = $gap_controller->get_active_gap($_SESSION['FT']['company_id']);
    $twig->addGlobal('gap', $gap);

    $misc = $misc_controller->get_active_misc($_SESSION['FT']['company_id']);
    $twig->addGlobal('misc', $misc);
    
    $warranty = $warranty_controller->get_active_warranty($_SESSION['FT']['company_id']);
    $twig->addGlobal('warranty', $warranty);

    $twig->addGlobal('hide_customize_btn', 'yes');
        
    $setting = new dealsetting();
    $settinglist = new dealsettinglist();

    $settinglist->load_company_id($_SESSION['FT']['company_id']);
    $twig->addGlobal('flooring_section', $settinglist->__get('flooringsection'));
    $twig->addGlobal('doc_fee_section', $settinglist->__get('doc_fee'));
    $twig->addGlobal('pack_section', $settinglist->__get('pack'));
    $twig->addGlobal('pickuppayments_section', $settinglist->__get('pickuppayments'));
    
    $nonce = new nonce();
    $nonce->add('action', 'insert');
    $nonce->add('company_id', $_SESSION['FT']['company_id']);
    $nonce->save();
    
    $twig->addGlobal('nonce_list', $nonce->get_nonce_list());
    $twig->addGlobal('sec_deals_permission', $_SESSION['FT']['SEC_DEALS_PERMISSION']);
    if(isset($_SESSION['errors'])){
        $twig->addGlobal("errors", $_SESSION['errors']);
        unset($_SESSION['errors']);
    }
    if(isset($_SESSION['new_deal_info']))
    {
        // var_dump($_SESSION['new_deal_info']);
        // exit();

        if(isset($_SESSION['new_deal_info']['newcar'])){
            $_SESSION['new_deal_info']['newcar'] = '1';
        }
        if(isset($_SESSION['new_deal_info']['flooring_paid'])){
            $_SESSION['new_deal_info']['flooring_paid'] = '1';
        }
        if(isset($_SESSION['new_deal_info']['financed_deal'])){
            $_SESSION['new_deal_info']['financed_deal'] = '1';
        }
        if(isset($_SESSION['new_deal_info']['funded'])){
            $_SESSION['new_deal_info']['funded'] = '1';
        }
        if(isset($_SESSION['new_deal_info']['approved'])) {
            $_SESSION['new_deal_info']['approved'] = '1';
        }

        $twig->addGlobal('deal', $_SESSION['new_deal_info']);
        if(isset($_SESSION['new_deal_info']['salespeople']))
        $twig->addGlobal('sales_people', $_SESSION['new_deal_info']['salespeople']);

        $pickupPaymentArray = [];
        
            $pickup_id = $_SESSION['new_deal_info']['pickup_id'];
            $pickup_paid = $_SESSION['new_deal_info']['pickup_paid'];
            $pickup_delete = $_SESSION['new_deal_info']['pickup_delete'];
            $pickup_date_due = $_SESSION['new_deal_info']['pickup_date_due'];
            $pickup_amount = $_SESSION['new_deal_info']['pickup_amount'];
            $pickup_payment_method = $_SESSION['new_deal_info']['pickup_payment_method'];
            $pickup_note = $_SESSION['new_deal_info']['pickup_note'];
            $pickUpReport = new pickuppaymentreport;
            // $processor->commit();
            for($i = 0; $i < sizeof($pickup_id); $i++){
                $payment['id'] = '0';
                $payment['paid'] = $pickup_paid[$i];
                $payment['deleted'] = $pickup_delete[$i];
                $payment['date_due'] = $pickup_date_due[$i];
                $payment['amount'] = $pickup_amount[$i];
                $payment['payment_method'] = $pickup_payment_method[$i];
                $payment['note'] = $pickup_note[$i];
                $payment['date_due_formatted'] = date_format(date_create($payment['date_due']), "m/d/Y");
                $payment['status'] = $pickUpReport->get_payment_status($payment);
                array_push($pickupPaymentArray, $payment);
            }
            //var_dump($pickupPaymentArray);
            $twig->addGlobal('pickup_payments', $pickupPaymentArray);
        }
        
        $nonce = new nonce();
        $nonce->add('action', 'new');
        $nonce->add('company_id', $_SESSION['new_deal_info']['company_id']);
        $nonce->save();
        
        $twig->addGlobal('nonce_list', $nonce->get_nonce_list());
        $twig->addGlobal('action', 'new');
        if(isset($_SESSION['new_deal_info']['doc_fee']))
        {
            $temp['value'] = $_SESSION['new_deal_info']['doc_fee'];
            $twig->addGlobal('doc_fee_section', $temp);            
        }
        if(isset($_SESSION['new_deal_info']['pack'])){
            $temp['value'] = $_SESSION['new_deal_info']['pack'];
            $twig->addGlobal('pack_section', $temp);      
        }
        //var_dump($pickupPaymentArray);
        // $twig->addGlobal('pickup_payments', $pickupPaymentArray);

        // remove new_deal_info
        unset($_SESSION['new_deal_info']);
    }
    //var_dump($nonce->get_nonce_list());
    
    /*
     * Render Templates
     */
    echo $twig->render('forms/tpl.deal.twig');
