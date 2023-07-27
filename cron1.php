<?php

  $server = "localhost";
  $username = "betafund_live";
  $password = ".X[Iq]p{g45y";
  $db = "betafund_funding";
  $conn = mysqli_connect($server, $username, $password, $db);
  $csv_url = "home1/betafund/public_ftp/incoming/EXPORT" ;
  $csv_doc = file_get_contents($csv_url);
  $tmp_file = tmpfile();
  fwrite($tmp_file, $csv_doc);
  fseek($tmp_file, 0);
  while ($line = fgetcsv($tmp_file)) {
    $temp['company_no'] = $line[0];
    $temp['Full_name'] = $line[1];//I guess company full name
    $temp['phone_number'] = $line[2];
    $temp['stock_number'] = $line[3];
    $temp['new/used'] = $line[4];
    $temp['year'] = $line[5];
    $temp['make'] = $line[6];
    $temp['model'] = $line[7];
    $temp['lender_name'] = $line[8];
    $temp['lessor_name'] = $line[9];
    $temp['trade_allowance'] = $line[10];
    $temp['term'] = $line[11];
    $csv_data[] = $temp ;
    /* mysql insert part */
    $sql = "INSERT into test_deal (`id`,`company_id`,`client_phone`,`year`,`stock`,`model`, `make`,`lender_id`)   values ('','" .$line[0]."','".$line[2]."','".$line[5]."','".$line[3]."','".$line[6]."', '".$line[7]."','".$line[8]."')";
    mysqli_query($conn, $sql) or die (mysql_error());

  }

  
  fclose($tmp_file);
    


?>