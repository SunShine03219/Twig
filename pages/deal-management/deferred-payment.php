<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();

$_SESSION['FT']['current_page'] = 'deferred';
$twig->addGlobal('session', $_SESSION['FT']);

if(isset($_GET['dropdown'])) {
    $_SESSION['FT']['topbar_filters'] = $_POST;
} else if(isset($_GET['table-list'])) {
    $controller = new dealcontroller();
    echo $controller->getDeferredTableList($_GET, $_SESSION['FT']['company_id']);
} else {
    $twig->addGlobal('page_title', 'Deferred Payments');
    // $controller = new pickuppaymentcontroller();
    // $output = $controller->invoke();

    $pickuppaymentreportObj = new pickuppaymentreport();
    $controller = new dealcontroller();
    $company_id = $_SESSION['FT']['company_id'];

    $select = 'SELECT
        pp.deal_id deal_id,
        if(DATE_FORMAT(pp.date_due, "%Y-%m-%d") < DATE_FORMAT(now(), "%Y-%m-%d"), "Over Due", if(DATE_FORMAT(pp.date_due, "%Y-%m-%d") > DATE_FORMAT(now(), "%Y-%m-%d"), "Waiting", "Due Today")) `status`,
        pp.id id,
        pp.date_due date_due,
        DATE_FORMAT(pp.date_due, "%m/%d/%Y") date_due_f,
        pp.amount amount,
        pp.coupon_value coupon_value,
        pp.coupon_id coupon_id,
        pm.caption method,
        pp.paid paid,
        pp.deleted deleted,
        d.deleted deleted,
        d.closed_dms closed_dms,
        d.funded funded,
        d.stock stock,
        d.locked LOCKED,
        d.date_sold date_sold,
        d.flooring_paid flooring_paid,
        d.newcar as newcar,
        GROUP_CONCAT(`s`.`name` SEPARATOR " & ") AS `sales_team`,
        d.client_name customer';
        
    $whereStatement = " WHERE paid = 0 AND d.company_id = $company_id ";
    $whereStatement = $controller->deferred_deals_filter($_SESSION['FT']['topbar_filters'], $company_id, $whereStatement);
    $joins = " INNER JOIN (
    SELECT
        id,
        company_id,
        stock,
        LOCKED,
        deleted,
        closed_dms,
        newcar,
        funded,
        date_sold,
        flooring_paid,
        client_name
    FROM
        deals) d ON pp.deal_id = d.id
    LEFT JOIN paymentmethods pm ON pp.payment_method = pm.id
    LEFT JOIN deals_to_sales m ON d.id = m.deal_id
    LEFT JOIN salespeople s ON m.sales_id = s.id";
    $groupBY = " GROUP BY pp.id, m.deal_id";

    $sql = $select.' FROM pickuppayments pp'.$joins.$whereStatement.$groupBY;

    $result = db_select_assoc_array($sql);

    $custom_result = array();
    $total = array();
    $totalToday = 0;
    $totalOverDue = 0;
    $totalWaiting = 0;
    if($result){
        $count = 0;
        $total['amount'] = 0.0;
        $total['deal'] = 'Total: ' . intval(count($result));
        $totalOverDue = 0;
        foreach($result as $row){
                              
            $total['amount'] += $row['amount'];
            $result[$count]['customer'] = $row['customer'];
            $result[$count]['deal'] = sprintf($sprint_deal_id, $row['deal_id'],$row['customer'], $row['stock']);
            if($row['deleted']){
                $result[$count]['paid'] = sprintf($sprint_action_undelete, $row['id']);
            } else {
                $result[$count]['paid'] = sprintf($sprint_action_pay,$row['id'], ($row['paid']==1?'unpay':'pay'), ($row['paid']==1?'Mark Unpaid':'Mark Paid'));
            }

            $result[$count]['log'] = sprintf($sprint_view_log, $row['deal_id']);
            $result[$count]['status'] = $pickuppaymentreportObj->get_payment_status($row);
            unset($row['deleted']);
            if($pickuppaymentreportObj->get_payment_status($row) == 'Over Due')
                $totalOverDue += $row['amount'];
            if($pickuppaymentreportObj->get_payment_status($row) == 'Due Today')
                $totalToday += $row['amount'];
            if($pickuppaymentreportObj->get_payment_status($row) == 'Waiting')
                $totalWaiting += $row['amount'];

            $count++;
        }

        $custom_result['info']['totalDdeals'] = $count;
        $custom_result['info']['totalDue'] = $total['amount'];
        $custom_result['info']['totalDueToday'] = $totalToday;
        $custom_result['info']['totalPastDue'] = $totalWaiting;
        $custom_result['info']['totalOverDue'] = $totalOverDue;

    }

    if(isset($_GET['type'])){
        if($_GET['type'] == "pagination" || $_GET['type'] == "sort"){
            echo json_encode($output['data']);
            exit();
        }
    }

    if ($custom_result['info'] != false) {
        // $dealCount = count($output);
        // $totalDue = 0;
        // $totalDueToday = 0;
        // $totalPastDue = 0;
        // $totalOverDue = 0;

        // foreach ($output as $deal) {
        //     $totalAmount = str_replace(',', '', $deal['amount']);
        //     $totalAmount = str_replace('$', '', $totalAmount);
        //     if ($deal['status'] == 'Waiting') {
        //         $totalPastDue += $totalAmount;
        //     } else if ($deal['status'] == 'Over Due') {
        //         $totalOverDue += $totalAmount;
        //     } else if ($deal['status'] == 'Due Today') {
        //         $totalDueToday += $totalAmount;
        //     }
        //     $totalDue += $totalAmount;

        // }
        // $averageFinanced = $totalFinanced / $dealCount;
        // $averageDiscount = $totalDiscount / $dealCount;
        
        $twig->addGlobal('deals', $output['data']);
        $twig->addGlobal('deal_count', $custom_result['info']['totalDdeals']);
        $twig->addGlobal('deal_total_due', $custom_result['info']['totalDue']);
        $twig->addGlobal('deal_today_due', $custom_result['info']['totalDueToday']);
        $twig->addGlobal('deal_past_due', $custom_result['info']['totalPastDue']);
        $twig->addGlobal('deal_over_due', $custom_result['info']['totalOverDue']);
    }

    //var_dump($output);
    //var_dump($_SESSION['FT']);

    /*
     * Render Templates
     */
    $_SESSION["current_page"] = "deferred";
    $_SESSION['FT']['query_string'] = $_SERVER['QUERY_STRING'];
    $delete_permission = ($_SESSION['FT']['SEC_DELETE']) ? 'yes' : 'no';
    $twig->addGlobal('delete_permission', $delete_permission);
    $twig->addGlobal('deals_dropdown', $_SESSION['FT']['deals_dropdown']);


    echo $twig->render('deal-management/tpl.deferred-payment.twig');
}
