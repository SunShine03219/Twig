<?php
error_reporting(E_ERROR | E_PARSE);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();

$twig->addGlobal('page_title', 'Lead Sources');
$twig->addGlobal('session', $_SESSION['FT']);

$newdate = date("j");
    $makedate;
    $yesterday;
    $firstdate;
    $lastdate;
    $prevmonfirst;
    $prevmonlast;
    $make;
    // $year = $_GET['year'];
    // $month = $_GET['month'];
    $leadsource = [];
    $year = date("Y");
    $month = date('m');
        $prevmonfirst = $year . "/" . $month-1 . "/" . "01";
        $prevmonlast = $year . "/" . $month . "/" . "31";
        $firstdate = $year . "/" . $month . "/" . "01";
        $lastdate = $year . "/" . $month . "/" . "31";
        if($newdate<10){
            $makedate = $year . "/" . $month . "/" . $newdate;
            $yesterday = $year . "/" . $month . "/" . $newdate-1;
        }
        else{
            $makedate = $year . "/" . $month . "/" . $newdate;
            $yesterday = $year . "/" . $month . "/" . $newdate-1;
        }

    $date1='2013/04/05';
    $mode = $_GET['mode'];
    $month = $_GET['month'];
    $year = $_GET['year'];
    $datestart=$_GET['datestart'];
    $dateend=$_GET['dateend'];
    $deleted = $_GET['deleted'];
    $newcar = $_GET['newcar'];
    $search = $_GET['search'];
    $company_id=$_SESSION['FT']['company_id'];
    $locked = '';
    if($_GET['locked'] != '' && $_GET['locked'] != 2){
        $locked =" AND locked=".$_GET['locked'];
    }

        if($deleted == null){
            if(trim($search) != '') {
                $search = "AND (ld.name LIKE '%". $search ."%') ";
            }
            $sql = "SELECT ld.id id, ld.name NAME, COUNT(d.id) deals, COUNT(*) total_lead FROM deals d LEFT JOIN lead ld ON d.lead_id = ld.id WHERE d.company_id=$company_id AND d.lead_id != 0 AND date_sold".$locked." ".$search." between '$firstdate' and '$makedate' GROUP BY d.lead_id ORDER BY d.lead_id ASC";
        }
        if($deleted == 2){
            // print_r("stop");exit();
            $sql = "SELECT ld.id id, ld.name NAME, COUNT(d.id) deals, COUNT(*) total_lead FROM deals d LEFT JOIN lead ld ON d.lead_id = ld.id WHERE d.company_id=$company_id AND d.lead_id != 0";
            if($_GET['newcar'] == 'newcar'){
                $sql .=" AND newcar=1";
            }
            if($_GET['locked'] != ''){
                $sql .=" AND locked=".$_GET['locked'];
            }
            if(trim($search) != '') {
                $sql .= " AND (ld.name LIKE '%". $search ."%') ";
            }
            if($_GET['mode'] == 'unlimited'){
                $sql .=" GROUP BY d.lead_id ORDER BY d.lead_id ASC";
            }
            if($_GET['mode'] == 'mtd'){
                $sql .=" AND date_sold between '$firstdate' and '$makedate'";
                $sql .=" GROUP BY d.lead_id ORDER BY d.lead_id ASC";
            }
            if($_GET['mode'] == 'today'){
                $sql .=" AND date_sold = '$makedate'";
                $sql .=" GROUP BY d.lead_id ORDER BY d.lead_id ASC";
            }
            if($_GET['mode'] == 'prevmon'){
                $sql .=" AND d.date_sold >='".date('Y-m-01', strtotime('first day of previous month')). "' AND " . " d.date_sold <= '".date('Y-m-31', strtotime('last day of previous month')). "'" ;
                // $sql .=" AND date_sold between '$prevmonfirst' and '$prevmonlast'";
                $sql .=" GROUP BY d.lead_id ORDER BY d.lead_id ASC";
            }
            if($_GET['mode'] == 'yesterday'){
                $sql .=" AND date_sold = '$yesterday'";
                $sql .=" GROUP BY d.lead_id ORDER BY d.lead_id ASC";
            }
            if($_GET['mode'] == 'daterange'){
                $sql .=" AND date_sold between '$datestart' and '$dateend'";
                $sql .=" GROUP BY d.lead_id ORDER BY d.lead_id ASC";
            }
            if($_GET['mode'] == 'custommonth'){
                $year = $_GET["year"];
                $month = $_GET["month"];
                $sql .= " AND d.date_sold >='".$year."-".$month."-01'" . " AND d.date_sold <='".$year."-".$month."-31' ";
                // $sql .=" AND date_sold between '$firstdate' and '$lastdate'";
                $sql .=" GROUP BY d.lead_id ORDER BY d.lead_id ASC";
            }
        }
        if($deleted != null && $deleted != 2){
            $sql = "SELECT ld.id id, ld.name NAME, COUNT(d.id) deals, COUNT(*) total_lead FROM deals d LEFT JOIN lead ld ON d.lead_id = ld.id WHERE d.company_id=$company_id AND d.lead_id != 0";
            if($_GET['newcar'] == 'newcar'){
                $sql .=" AND newcar=1";
            }
            if($_GET['locked'] != ''){
                $sql .=" AND locked=".$_GET['locked'];
            }

            if(trim($search) != '') {
                $sql .= " AND (ld.name LIKE '%". $search ."%') ";
            }

            if($_GET['mode'] == 'unlimited'){
                $sql .=" AND deleted=$deleted";
                $sql .=" GROUP BY d.lead_id ORDER BY d.lead_id ASC";
            }
            if($_GET['mode'] == 'mtd'){
                $sql .=" AND deleted=$deleted";
                $sql .=" AND date_sold between '$firstdate' and '$makedate'";
                $sql .=" GROUP BY d.lead_id ORDER BY d.lead_id ASC";
            }
            if($_GET['mode'] == 'today'){
                $sql .=" AND deleted=$deleted";
                $sql .=" AND date_sold = '$makedate'";
                $sql .=" GROUP BY d.lead_id ORDER BY d.lead_id ASC";
            }
            if($_GET['mode'] == 'prevmon'){
                $sql .=" AND deleted=$deleted";
                $sql .=" AND date_sold between '$prevmonfirst' and '$prevmonlast'";
                $sql .=" GROUP BY d.lead_id ORDER BY d.lead_id ASC";
            }
            if($_GET['mode'] == 'yesterday'){
                $sql .=" AND deleted=$deleted";
                $sql .=" AND date_sold = '$yesterday'";
                $sql .=" GROUP BY d.lead_id ORDER BY d.lead_id ASC";
            }
            if($_GET['mode'] == 'daterange'){
                $sql .=" AND deleted=$deleted";
                $sql .=" AND date_sold between '$datestart' and '$dateend'";
                $sql .=" GROUP BY d.lead_id ORDER BY d.lead_id ASC";
            }
            if($_GET['mode'] == 'custommonth'){
                $sql .=" AND deleted=$deleted";
                $sql .=" AND date_sold between '$firstdate' and '$lastdate'";
                $sql .=" GROUP BY d.lead_id ORDER BY d.lead_id ASC";
            }
        }

         //print_r($sql);exit;
        $result = db_select_assoc_array($sql);

        // print_r($result);exit;
        $total = array();
        $total[] = ['name','lead'];
        foreach($result as $rsl){
            $total[] = array($rsl["NAME"], $rsl['total_lead']);
        }
        $twig->addGlobal('totals', $total);
        $twig->addGlobal('leads', $result);

/*
 * Render Templates
 */
echo $twig->render('reports/tpl.lead-sources.twig');