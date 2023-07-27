<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
define('DB_CONNECTION_RO', 'ro');
define('DB_CONNECTION_RW', 'rw');
define('DB_SERVER', 'localhost');
define('DB_NAME', 'fundingt_betafund_funding');
define('DB_USER_RO', 'fundingt_live');
define('DB_PASS_RO', '.X[Iq]p{g45y');
define('DB_USER_RW', 'fundingt_live');
define('DB_PASS_RW', '.X[Iq]p{g45y');
  
  $conn = mysqli_connect(DB_SERVER, DB_USER_RO, DB_PASS_RO, DB_NAME);
   $csv_url = "public_ftp/incoming/EXPORT";
   //$csv_url = "public_ftp/EXPORT" ;

  if(file_exists($csv_url)){
    
      $csv_doc = file_get_contents($csv_url);
      $tmp_file = tmpfile();
      fwrite($tmp_file, $csv_doc);
      fseek($tmp_file, 0);
    
      $company_list;
    
      while ($line = fgetcsv($tmp_file)) {
        $temp['company_no'] = null;
        switch ($line[0]) {
          case 'GC1':
              $temp['company_no'] = 92;
              $company_list[$temp['company_no']] = "NW Chevy Of Bellingham";
              break;
          case 'GC4':
              $temp['company_no'] = 94;
              $company_list[$temp['company_no']] = "NW Auto Hub";
              break;
          case 'GC5':
              $temp['company_no'] = 83;
              $company_list[$temp['company_no']] = "Bellingham Nissan";
              break;
          default:          
              break;
        }
    
    
        $temp['full_name'] = $line[1]; //I guess company full name
        $temp['phone_number'] = $line[2];
        $temp['stock_number'] = trim($line[4]);
        $temp['new/used'] = $line[5];
        $temp['year'] = $line[6];
        $temp['make'] = $line[7];
        $temp['model'] = $line[8];
        $temp['lender_name'] = $line[9];
        $temp['lessor_name'] = $line[10];
        $temp['trade_allowance'] = $line[11];
        $temp['term'] = $line[12];
        $csv_data[] = $temp ;
        /* mysql insert part */
        if($temp['company_no'] != null){
          $sql = "INSERT into deals_import (
            `id`,
            `company_id`,
            `client_name`,
            `client_phone`,
            `year`,
            `stock`,
            `model`, 
            `make`,
            `lender_id`,
            `date_sold` ) 
              values ('','" . 
            $temp['company_no']."','" .
            addslashes($temp['full_name'])."',  '". 
            $temp['phone_number']."', '".
            $temp['year']."', '". 
            $temp['stock_number']."','".
            $temp['model']."', '". 
            $temp['make']."','".
            $temp['lender_name']."', 
            now())";
          mysqli_query($conn, $sql) or die (mysqli_error($conn));
          $temp['company_no']=null;
        }
        //****
      }
      fclose($tmp_file); 
       unlink($csv_url);
      if($csv_doc){
        $title = "";
        foreach($company_list as $key => $value){
          $title .= $value . ", ";
        }
    
        $date = date ("y:m:d:H:m:s");
        $history_sql = "INSERT into deal_import_history(`id`,`title`,`date`) values(
          '','".addslashes($title)."','".$date."')";
      }
      mysqli_query($conn,$history_sql) or die(mysqli_error($conn));
  }

?>