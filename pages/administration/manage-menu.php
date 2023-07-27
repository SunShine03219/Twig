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
if (isset($_GET['id'])) {
    $where = array('company_id' => $_GET['com_id'], 'menu_id' => $_GET['id']);
    $sql = "Update manage_menu SET status = !status where menu_id = '".$_GET['id']."' and company_id = '".$_SESSION['FT']['company_id']."'";
    $result = db_update_bare($sql);
    if (!$result) {
        $data = array('menu_id' => $_GET['id'], 'company_id' => $_SESSION['FT']['company_id'], 'status' => 0);
        db_insert_assoc_one('manage_menu',$data);
    }
    
    header("location: /admin/manage-menu");
} else {
    // $sql = "Update pickuppayments SET paid = 1 where id = '".$pickuppayment_id."'";
    // $result = db_update_bare($sql);
    // header("location: /deals/deferred");db_select_assoc

    $sql = "SELECT a.id, a.name, b.status FROM menus AS a RIGHT JOIN manage_menu as b ON a.id = b.menu_id AND b.company_id = ".$_SESSION['FT']['company_id']."";
    $manage_menu = db_select_assoc_array($sql);
    $result = db_select('menus');
    $buf = arraY();
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
    $twig->addGlobal('menus',$buf);
    
    
    /*
     * Render Templates
     */
    echo $twig->render('administration/tpl.manage-menu.twig');
}
