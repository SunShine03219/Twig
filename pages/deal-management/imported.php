<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();

$twig->addGlobal('session', $_SESSION['FT']);

if(isset($_GET['import_id'])){
    $output = Array();
    $id = isset($_GET['import_id']) ? $_GET['import_id'] : null;
    if(!empty($id)){
        $where = array('id' => $id);
        $data = db_select_one('deals_import', $where);
        $stock = trim($data['stock']);
        $exists = null;
        $message = null;
        if(!empty($stock)){
            $exists =  db_select_assoc("SELECT count(*) total FROM deals WHERE trim(stock) = '{$stock}';")['total'];
        }
        
        unset($data['id']);
        $result =  db_insert_assoc_one('deals',$data);

        $sql = "DELETE from deals_import WHERE id='{$id}' ";
        db_delete_bare($sql);

        $output = array(
            'success' => true,
            'type' => (empty($exists) ? 'success' : 'warning'),
            'message' => (empty($exists) ? 'Deal Added Successfully' : 'This stock number already in use!'),
            'data' => $result
        );
    }else{
        $output = array(
            'success' => false,
            'message' => 'No deal has been set.'
        );
    }
    exit(json_encode($output));
} else if(isset($_GET['dropdown'])) {
    $_SESSION['FT']['topbar_filters'] = $_POST;
} else if(isset($_GET['table-list'])) {
    $controller = new dealcontroller();
    echo $controller->getImportedTableList($_GET, $_SESSION['FT']['company_id']);
} else {
    $twig->addGlobal('page_title', 'Imported Deals');
    //$controller = new dealcontroller();


    $twig->addGlobal('deals', $imported_deals);

    $_SESSION["current_page"] = "imported";
    $_SESSION['FT']['query_string'] = $_SERVER['QUERY_STRING'];

    echo $twig->render('deal-management/tpl.imported.twig');
}


// $sql = "select * from deals_import where company_id = " . $_SESSION['FT']['company_id'];
// $sql = "SELECT id, client_name, make, model, stock, date_sold, client_phone, lender_id, `year` FROM `deals_import` WHERE company_id = " . $_SESSION['FT']['company_id'] . " group by client_name, make, model, stock, date_sold ";

// if(isset($_GET['type'])){
//     $sql .= ' ORDER BY '.$_GET['field'].' '.$_GET['direction'];
// }else{
//     if($orderclause == null){
//         $sql .= ' ORDER BY date_sold DESC';
//     }else{
//         $sql .= $orderclause;
//     }
// }

// $totalSql = $sql;
// if(isset($_GET['field'])){
//     if($_GET['type'] == 'pagination'){
//         $totalResults = isset($_GET['row_count']) ? $_GET['row_count'] : 100;
//         $sql .= " limit ". $totalResults .", 100";
//     }else{
//         $sql .= " limit 0, 100";
//     }
// }else{
//     $sql .= " limit 0, 100";
// }

// $imported_deals = db_select_assoc_array($sql);

// if(isset($_GET['field']))
// {
//     echo json_encode($imported_deals);
//     exit;
// }

// if(isset($_GET['import_id'])){
    
// }

