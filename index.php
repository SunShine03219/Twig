<?php

/*
 * Database variables for all enviroments
 */
require_once '_config/config.enviroment.php';
/*
 * Load primary functions
 */
include_once '_config/config.php';

/*
 * Load Template Engine
 */

require_once '_includes/twig/vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('_templates/default/components/template');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);
function PrintVar($_var){
    echo '<pre>';
    print_r($_var);
    echo '</pre>';
}
/*
$twig = new \Twig\Environment($loader, [
    'cache' => '_templates/default/cache',
]);
*/
// print_r($_SERVER['QUERY_STRING']);die();
// $_GET['q'] = $_SERVER['QUERY_STRING'];
$_GET['q'] = array_search (null, $_GET);


if(isset($_GET['clear-filters'])) {
    $_SESSION['FT']['topbar_filters'] = '';
    echo json_encode(array('url' => $_POST['route']));exit;
} 

if(isset($_POST['page']) && $_POST['page'] == 'report') {
    $_SESSION['FT']['report_page'] = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_POST['route'];
    echo json_encode(array('url' => $_POST['route']));exit;
} 

$uriStr = explode('/', $_GET['q']);
$today_date = getdate();
$twig->addGlobal('today_date', $today_date);

// get params start
$filters = array();
if(isset($uriStr[0]) && $uriStr[0] != 'deals'){
    // Filters for reports pages
    $filters = $_GET;
    $twig->addGlobal('mode', isset($filters['mode']) ? $filters['mode'] : "mtd");
    $twig->addGlobal('search_dt', isset($filters['search']) ? @trim($filters['search']) : "");
    $twig->addGlobal('lender_id', isset($filters['lender_id']) ? $filters['lender_id'] : "0");
} else{
    $queryString = $_SESSION['FT']['query_string'];
    $dealsFilters = isset($_SESSION['FT']['topbar_filters']) ? $_SESSION['FT']['topbar_filters'] : '';
    // Filters for deals pages
    $filters = $dealsFilters;
    if($queryString !='deals' && ($queryString != $_GET['q'])){
        $_SESSION['FT']['topbar_filters'] = '';
        $filters = array();
    }
    
    $twig->addGlobal('search', isset($filters['search']) ? $filters['search'] : "");
    $twig->addGlobal('mode', isset($filters['mode']) ? $filters['mode'] : "mtd");
}

$twig->addGlobal('locked', isset($filters['locked']) ? $filters['locked'] : "");
$twig->addGlobal('include_deleted', isset($filters['include_deleted']) ? $filters['include_deleted'] : "0");
$twig->addGlobal('paid', isset($filters['paid']) ? $filters['paid'] : "");
$twig->addGlobal('deleted', isset($filters['deleted']) ? $filters['deleted'] : "0");
$twig->addGlobal('datestart', isset($filters['datestart']) ? $filters['datestart'] : "");
$twig->addGlobal('month', isset($filters['month']) ? $filters['month'] : "");
$twig->addGlobal('year', isset($filters['year']) ? $filters['year'] : "");
$twig->addGlobal('dateend', isset($filters['dateend']) ? $filters['dateend'] : "");
$twig->addGlobal('newcar', isset($filters['newcar']) ? $filters['newcar'] : "");
$twig->addGlobal('curYear', date('Y'));
$twig->addGlobal('curMonth', date('n'));

//  get params end

// if (!isset($_GET['q']) || $_GET['q'] == false) {
if (!isset($_GET['q'])) {
    include 'pages/access/login.php';
} else {
    if(isset($_SESSION['FT']['company_id'])) {
        $sql = "SELECT a.id, a.url, a.full_url, a.name, b.status FROM menus AS a RIGHT JOIN manage_menu as b ON a.id = b.menu_id AND b.company_id = ".$_SESSION['FT']['company_id']."";
        $manage_menu = db_select_assoc_array($sql);
        $result = db_select('menus');
        $buf = array();
        foreach($result as $item1) {
            $flag = 0;
            foreach($manage_menu as $item2) {
                if($item1['id'] == $item2['id']) {
                    array_push($buf, $item2);
                    $flag = 1;
                    break;
                }
            }
            if(!$flag) array_push($buf, $item1);
        }
    
        $twig->addGlobal('menus_view', $buf);
    }
    $twig->addGlobal('navigation', $_GET['q']);

    switch ($_GET['q']) {
        case 'profile/user':
            // $twig->addGlobal('navigationSection', 'profile');
            include 'pages/profile/profile.php';
            break;
        case 'admin/company':
            $twig->addGlobal('navigationSection', 'admin');
            include 'pages/administration/company.php';
            break;
        case 'admin/users':
            $twig->addGlobal('navigationSection', 'admin');
            include 'pages/administration/users.php';
            break;
        case 'admin/sales':
            $twig->addGlobal('navigationSection', 'admin');
            include 'pages/administration/sales.php';
            break;
        case 'admin/desk':
            $twig->addGlobal('navigationSection', 'admin');
            include 'pages/administration/desk.php';
            break;
        case 'admin/finance':
            $twig->addGlobal('navigationSection', 'admin');
            include 'pages/administration/finance.php';
            break;
        // case 'admin/coupons':
        //     $twig->addGlobal('navigationSection', 'admin');
        //     include 'pages/administration/coupon.php';
        //     break;
        case 'admin/lending':
            $twig->addGlobal('navigationSection', 'admin');
            include 'pages/administration/lending.php';
            break;
        case 'admin/flooring':
            $twig->addGlobal('navigationSection', 'admin');
            include 'pages/administration/flooring.php';
            break;
        case 'admin/gap':
            $twig->addGlobal('navigationSection', 'admin');
            include 'pages/administration/gap.php';
            break;
        case 'admin/warranty':
            $twig->addGlobal('navigationSection', 'admin');
            include 'pages/administration/warranty.php';
            break;
        case 'admin/settings':
            $twig->addGlobal('navigationSection', 'admin');
            include 'pages/administration/settings.php';
            break;
        case 'admin/payment':
            $twig->addGlobal('navigationSection', 'admin');
            include 'pages/administration/payment.php';
            break;

        case 'admin/manage-menu':
            $twig->addGlobal('navigationSection', 'admin');
            include 'pages/administration/manage-menu.php';
            break;
        case 'deals':
            $twig->addGlobal('navigationSection', 'deals');
            include 'pages/deal-management/unfunded.php';
            break;
        case 'deals/approved':
            $twig->addGlobal('navigationSection', 'deals');
            include 'pages/deal-management/approved.php';
            break;
        case 'deals/close':
            $twig->addGlobal('navigationSection', 'deals');
            include 'pages/deal-management/close.php';
            break;
        case 'deals/deferred':
            $twig->addGlobal('navigationSection', 'deals');
            include 'pages/deal-management/deferred-payment.php';
            break;
        case 'deals/delete':
            $twig->addGlobal('navigationSection', 'deals');
            include 'pages/deal-management/delete.php';
            break;
        case 'deals/floored':
            $twig->addGlobal('navigationSection', 'deals');
            include 'pages/deal-management/floored.php';
            break;
        case 'deals/funded':
            $twig->addGlobal('navigationSection', 'deals');
            include 'pages/deal-management/funded.php';
            break;
        case 'deals/new':
            $twig->addGlobal('navigationSection', 'deals');
            include 'pages/deal-management/new.php';
            break;
        case 'deals/search':
            $twig->addGlobal('navigationSection', 'deals');
            include 'pages/deal-management/search.php';
            break;
        case 'deals/imported':
            $twig->addGlobal('navigationSection', 'deals');
            include 'pages/deal-management/imported.php';
            break;
        case 'deals/unfunded':
            $twig->addGlobal('navigationSection', 'deals');
            include 'pages/deal-management/unfunded.php';
            break;
        case 'deals/view':
            $twig->addGlobal('navigationSection', 'deals');
            include 'pages/deal-management/view.php';
            break;
        case 'deals/pending':
            $twig->addGlobal('navigationSection', 'deals');
            include 'pages/deal-management/pending.php';
            break;
            
        case 'error/admin':
            include 'pages/access/error.php';
            break;
        case 'error/support':
            include 'pages/access/error.php';
            break;
        case 'login':
            include 'pages/access/login.php';
            break;
        case 'register':
            include 'pages/access/register.php';
            break;
        case 'forgot':
            include 'pages/access/forgot.php';
            break;
        case 'verify':
            include 'pages/access/verify.php';
            break;
        case 'reset_password':
            include 'pages/administration/users.php';
            break;
        case 'logout':
            include 'pages/access/logout.php';
            break;
        case 'reports/company-gross':
            $twig->addGlobal('navigationSection', 'reports');
            include 'pages/reports/company-gross.php';
            break;
        case 'reports/desk-manager':
            $twig->addGlobal('navigationSection', 'reports');
            include 'pages/reports/desk-manager.php';
            break;
        case 'reports/sales-people':
            $twig->addGlobal('navigationSection', 'reports');
            include 'pages/reports/sales-people.php';
            break;
        case 'reports/finance':
            $twig->addGlobal('navigationSection', 'reports');
            include 'pages/reports/finance.php';
            break;
        case 'reports/lenders':
            $twig->addGlobal('navigationSection', 'reports');
            include 'pages/reports/lenders.php';
            break;
        case 'reports/flooring':
            $twig->addGlobal('navigationSection', 'reports');
            include 'pages/reports/flooring.php';
            break;
        case 'reports/funded':
            $twig->addGlobal('navigationSection', 'reports');
            include 'pages/reports/funded.php';
            break;
        case 'reports/sales-board':
            $twig->addGlobal('navigationSection', 'reports');
            include 'pages/reports/sales-board.php';
            break;
        case 'reports/gross-w-vehicle':
            $twig->addGlobal('navigationSection', 'reports');
            include 'pages/reports/gross-w-vehicle.php';
            break;
        case 'reports/lead-sources':
            $twig->addGlobal('navigationSection', 'reports');
            include 'pages/reports/lead-sources.php';
            break;
        case 'reports/finance-approval':
            $twig->addGlobal('navigationSection', 'reports');
            include 'pages/reports/finance-approval.php';
            break;
        case 'reports/time-to-fund':
            $twig->addGlobal('navigationSection', 'reports');
            include 'pages/reports/time-to-fund.php';
            break;
        case 'reports/finance-penetration':
            $twig->addGlobal('navigationSection', 'reports');
            include 'pages/reports/finance-penetration.php';
            break;
        case 'reports/gross-new':
            $twig->addGlobal('navigationSection', 'reports');
            include 'pages/reports/gross-new.php';
            break;
        case 'reports/gross-used':
            $twig->addGlobal('navigationSection', 'reports');
            include 'pages/reports/gross-used.php';
            break;
        case 'reports/trade-in':
            $twig->addGlobal('navigationSection', 'reports');
            include 'pages/reports/trade-in.php';
            break;
        case 'superuser/affiliate':
            $twig->addGlobal('navigationSection', 'superuser');
            include 'pages/superuser/affiliate.php';
            break;
                    
        case 'superuser/imported':
            //  exit("dsf");
            $twig->addGlobal('navigationSection', 'superuser');
            include 'pages/superuser/imported.php';
            break;
    
        case 'superuser/impersonate':
            $twig->addGlobal('navigationSection', 'superuser');
            include 'pages/superuser/impersonate.php';
            break;
        case 'superuser/customers':
            $twig->addGlobal('navigationSection', 'superuser');
            include 'pages/superuser/customers.php';
            break;
        case 'superuser/customers_bill':
            $twig->addGlobal('navigationSection', 'superuser');
            include 'pages/superuser/customers_bill.php';
            break;
        case 'superuser/subscriptions':
            $twig->addGlobal('navigationSection', 'superuser');
            include 'pages/superuser/subscriptions.php';
            break;
        case 'superuser/coupons':
            $twig->addGlobal('navigationSection', 'superuser');
            include 'pages/superuser/coupon.php';
            break;
        case 'messages/notifications':
            $twig->addGlobal('navigationSection', 'messages');
            include 'pages/messages/notifications.php';
            break;
        case 'togglemodal_php':
            $twig->addGlobal('navigationSection', 'deals');
            include 'pages/deal-management/new.php';
            break;
        case 'modals_php':
            $twig->addGlobal('navigationSection', 'messages');
            include 'pages/messages/modals.php';
            break;
        default:
            // die(substr($_GET['q'],0,10));
            if(substr($_GET['q'],0,10)=='deals/view')
            {
                $id=substr($_GET['q'],11,strlen($_GET['q']));
                $twig->addGlobal('navigationSection', 'deals');
                include 'pages/deal-management/view.php?'.$id;
                break;
            }else if($_GET['q'] == "deleteDeal"){
                $deal_id = $_POST['id'];
                $sql = "Update deals SET deleted = 1, closed_dms = 1 where id = '".$deal_id."'";
                $result = db_update_bare($sql);

                echo("success");
                break;
            }else if($_GET['q'] == "deferred"){
                $pickuppayment_id = $_GET['id'];
                $sql = "Update pickuppayments SET paid = 1 where id = '".$pickuppayment_id."'";
                $result = db_update_bare($sql);
                header("location: /deals/deferred");
                break;
            }
            header("location: /login");
            break;
    }
}
?>