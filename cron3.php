<?php

  $server = "localhost";
  $username = "betafund_live";
  $password = ".X[Iq]p{g45y";
  $db = "betafund_funding";
  $conn = mysqli_connect($server, $username, $password, $db);
  $csv_url = "/home1/betafund/public_ftp/incoming/EXPORT" ;
  $csv_doc = file_get_contents($csv_url);
  $tmp_file = tmpfile();
  fwrite($tmp_file, $csv_doc);
  fseek($tmp_file, 0);
  while ($line = fgetcsv($tmp_file)) {
    $temp['company_no'] = null;
    switch ($line[0]) {
      case 'GC1':
          $temp['company_no'] = 27;
          break;      
      case 'GC5':
          $temp['company_no'] = 83;
          break;
      default:          
          break;
    }
    
    $temp['full_name'] = $line[1];//I guess company full name
    $temp['phone_number'] = $line[3];
    $temp['stock_number'] = $line[4];
    $temp['new/used'] = $line[5];
    $temp['year'] = $line[6];
    $temp['make'] = $line[7];
    $temp['model'] = $line[8];
    $temp['lessor_name'] = $line[9];
    $temp['lender_name'] = $line[10];
    $temp['trade_allowance'] = $line[11];
    $temp['term'] = $line[12];
    $csv_data[] = $temp ;
    /* mysql insert part */
    if($temp['company_no'] != null){
      $sql = "INSERT into deals (`id`,`company_id`,`client_name`,`client_phone`,`year`,`stock`,`model`, `make`,`lender_id`,`date_sold` )   values ('','" . $temp['company_no']."','" .$temp['full_name']."',  '". $temp['phone_number']."', '".$temp['year']."', '". $temp['stock_number']."','".$temp['model']."', '". $temp['make']."','".$temp['lender_name']."', now())";
      mysqli_query($conn, $sql) or die (mysql_error());
      $temp['company_no']=null;
    }
    
    
    //****
  }
  fclose($tmp_file); 
  unlink($csv_url);
  if($csv_doc){
      $title = date("y-m-d H:m:s")." : Port Data" ;
      $date = date ("y:m:d:H:m:s");
      $history_sql = "INSERT into deal_port_history(`id`,`title`,`date`) values('','".$title."','".$date."')";
  }
   mysqli_query($conn,$history_sql) or die(mysql_error());

?>