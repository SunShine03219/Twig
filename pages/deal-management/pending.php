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

$twig->addGlobal('session', $_SESSION['FT']);

if(isset($_GET['dropdown'])) {
    $_SESSION['FT']['topbar_filters'] = $_POST;
} else if(isset($_GET['table-list'])) {
    $controller = new dealcontroller();

    echo $controller->getPendingTableList($_GET, $_SESSION['FT']['company_id']);
} else {

$twig->addGlobal('page_title', 'Pending Documents Deals');
$twig->addGlobal('session', $_SESSION['FT']);
$_SESSION['current_page'] = 'pending';
$_SESSION['FT']['current_page'] = 'pending';
$_SESSION['FT']['query_string'] = $_SERVER['QUERY_STRING'];
// $controller = new dealcontroller();
// $output = $controller->invoke('pending');

// if(isset($_GET['type'])){
//     if($_GET['type'] == "pagination" || $_GET['type'] == "sort"){
//         echo json_encode($output);
//         exit();
//     }
// }

// $twig->addGlobal('deals', $output);



echo $twig->render('deal-management/tpl.pending.twig');
}