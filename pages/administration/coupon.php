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

$twig->addGlobal('session', $_SESSION['FT']);

if(isset($_POST['type']) && $_POST['type'] == 'deleteCoupon'){
        $coupon = new coupon();
        $id = $_POST['id'];
        if (!$coupon->delete_one($id)) {
            //$twig->addGlobal('error', 'Coupon is not deleted Deleted.');
            echo json_encode(403); exit();
        } else {
            echo json_encode("success"); exit();
            //header('Location: /admin/coupons');
        }
        //header('Location: /admin/coupons');
}

if (isset($_POST['id'])) {
    if (isset($_POST['action']) && $_POST['action'] == 'reset') {
        $id = intval($_POST['id']);
        $user = new coupon();
        if ($user->make_me_into($id)) {
            
        } else {
            $twig->addGlobal('error', 'Unable to retrieve coupon ' . $id);
        }
    } else {
        $id = intval($_POST['id']);
        $coupon = new coupon();
        if ($coupon->make_me_into($id)) {
            $coupon->from_array($_POST);
            if(!$coupon->update_one()) {
                $twig->addGlobal('error', 'Unable to update coupon.');
            } else {
                $twig->addGlobal('error', 'Unable to update coupon.');
                header('Location: /admin/coupons');
            }
        } else {
            $twig->addGlobal('error', 'Unable to retrieve coupon ' . $id);
        }
    }
} else if (isset($_POST['action']) && $_POST['action'] == 'insert') {

    $nonce = new nonce($_POST['nonce']);
    $coupon = new coupon();
    if (!$coupon->validate_new($_POST)){
        $twig->addGlobal('error', 'Name already in coupon.');
        $_SESSION['error'] = 'Name already in coupon.';
    } else {
        //$coupon->from_array($_POST);
        if (!$coupon->insert_new()) {
            $twig->addGlobal('error', 'Unable to create new coupon.');
            $_SESSION['error'] = 'Unable to create new coupon.';
        } else {
            header('Location: /admin/coupons');
        }
    }
    
} else if (isset($_GET['id'])) {
    if (isset($_GET['action']) && $_GET['action'] == 'delete') {
        $coupon = new coupon();
        $id = $_GET['id'];
        if (!$coupon->delete_one($id)) {
            //$twig->addGlobal('error', 'Coupon is not deleted Deleted.');
            echo json_encode(403); exit();
        } else {
            echo json_encode("success"); exit();
            //header('Location: /admin/coupons');
        }
        header('Location: /admin/coupons');
    } else {
        $twig->addGlobal('page_title', 'Edit Coupon');
        $twig->addGlobal('session', $_SESSION['FT']);

        $coupon = new coupon();
        $id = $_GET['id'];
        if($coupon->make_me_into($id)){
            $nonce = new nonce();
            $nonce->add('action', 'update');
            $nonce->add('id', $id);
            $nonce->save();

			$coupons_arr = $coupon->to_array();
            $twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());
            $twig->addGlobal('coupon', $coupons_arr);
        } else {
            $twig->addGlobal('error', 'Coupon could not be located.');
        }
        /*
         * Render Templates
         */
        echo $twig->render('administration/tpl.coupon.twig');
    }
} else if (isset($_GET['action']) && $_GET['action'] == 'new') {

    $twig->addGlobal('page_title', 'New Coupon');
    $twig->addGlobal('session', $_SESSION['FT']);
    $twig->addGlobal('action', 'insert');

    $nonce = new nonce();
    $nonce->add('action', 'insert');
    $nonce->save();
    $controller = new couponcontroller();

    $coupon = array(
        'coupon' => $controller->generateRandomString(10)
    );
    $twig->addGlobal('hidden_input_array', $nonce->get_nonce_list());
    $twig->addGlobal('coupon', $coupon);

    /*
     * Render Templates
     */

    echo $twig->render('administration/tpl.coupon.twig');
} else if(isset($_GET['dropdown'])) {
    if(isset($_POST['status']) && $_POST['status'] == 'all' || $_POST['status'] == 'active' || $_POST['status'] == 'inactive') {
        $_SESSION['FT']['coupons_dropdown'] = $_POST['status'];
        
    }
} else if(isset($_GET['get-coupon'])) {
    $controller = new couponcontroller();
    echo $controller->generateRandomString(10);
    
} else if(isset($_GET['validate']) && $_GET['validate'] == 1 && isset($_GET['coupon'])) {
    $coupon = new coupon();
    $couponVal = $_GET['coupon'];
    $couponRecord = $coupon->get_coupon_by_value($couponVal);
    if ($couponRecord) {
        $controller = new couponcontroller();
        $validated = $controller->validateCoupon($couponRecord);
        if($validated){
            echo json_encode(array('status' => true, 'coupon'=> $couponRecord, 'message' => 'Coupon is Verified.'));
        }else{
            echo json_encode(array('status' => false, 'message' => 'Coupon is not Verified.'));
        }
    } else {
        echo json_encode(array('status' => false, 'message' => 'Coupon not found or Coupon is not active.'));
    }
    
    
} else if(isset($_GET['table-list'])) {
    $controller = new couponcontroller();

    echo $controller->getTableList($_GET, $_SESSION['FT']['coupons_dropdown'], $_SESSION['FT']['company_id']);
}else {
    $twig->addGlobal('page_title', 'Coupons');
    $twig->addGlobal('session', $_SESSION['FT']);
    $twig->addGlobal('error', $_SESSION['error']);
    unset($_SESSION['error']);
    // $controller = new couponcontroller();
    // $output = $controller->invoke();
    // $twig->addGlobal('coupons', $output);
    if(!isset($_SESSION['FT']['coupons_dropdown'])) {
        $_SESSION['FT']['coupons_dropdown'] = "all";
    }
    $twig->addGlobal('coupons_dropdown', $_SESSION['FT']['coupons_dropdown']);
    /*
     * Render Templates
     */
    echo $twig->render('administration/tpl.coupons.twig');
}