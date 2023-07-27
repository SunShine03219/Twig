<?php
/*
 * Confirm Server Name
 */
 /*
if ($_SERVER['SERVER_NAME'] != 'fundingtracker') {
    die('Invalid Server Name');
}
*/
 if ($_SERVER['SERVER_NAME'] == 'fundingtracker.net' || $_SERVER['SERVER_NAME'] == 'www.fundingtracker.net') {
    define('DB_CONNECTION_RO', 'ro');
    define('DB_CONNECTION_RW', 'rw');
    define('DB_SERVER', 'db-fundingtracker.c7c1e10gnn01.us-east-1.rds.amazonaws.com');
    define('DB_NAME', 'fundingtracker');
    define('DB_PORT', '3306');
    define('DB_USER_RO', 'ufunding');
    define('DB_PASS_RO', 'bieAJs&CS$jfxS7z');
    define('DB_USER_RW', 'ufunding');
    define('DB_PASS_RW', 'bieAJs&CS$jfxS7z');
    define('PATH_CRON', '/home/ubuntu/fundingtracker/');

    //LIVE
    define('STRIPE_KEY', 'pk_live_51IulAeLiGGzfFa3lczCH37X59VNSLe9cVvAh2QXitFjwfcO6kFMtUtDvNzkPT7UHukl5EaqWxz4qHdpdOxnb4cQQ00KrfVc1xb');
    define('STRIPE_SECRET', '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');
    //define('STRIPE_KEY', 'pk_test_51IulAeLiGGzfFa3l7NOOzoQZrx2sNx1HvuRWsA0sx6RQqwKkGqxzFWDiDUDsQ2QzRxUxdLRTMYEjJRqHacUTkMBZ00yXt17gs0');
    //define('STRIPE_SECRET', 'sk_test_51IulAeLiGGzfFa3lpFrZLZJpPCoeO7118FQrL4czRLnIZWwHb4oHL8IfSkauQ7DBx7MIEzjs7pYIA6Hq1lNy6KHF00GTp69rby');


 }else if ($_SERVER['SERVER_NAME'] == 'fundingtracker.tk') {

    define('DB_CONNECTION_RO', 'ro');
    define('DB_CONNECTION_RW', 'rw');
    define('DB_SERVER', 'development.c7c1e10gnn01.us-east-1.rds.amazonaws.com');
    // define('DB_NAME', 'fundingTrackerDev');
    define('DB_NAME', 'fundingTrackerProd');
    define('DB_PORT', '3306');
    define('DB_USER_RO', 'udev');
    define('DB_PASS_RO', 'oJEQPK6i4A8q9zbJ');
    define('DB_USER_RW', 'udev');
    define('DB_PASS_RW', 'oJEQPK6i4A8q9zbJ');

    // define('DB_SERVER', 'db-fundingtracker.c7c1e10gnn01.us-east-1.rds.amazonaws.com');
    // define('DB_NAME', 'fundingtracker');
    // define('DB_PORT', '3306');
    // define('DB_USER_RO', 'ufunding');
    // define('DB_PASS_RO', 'bieAJs&CS$jfxS7z');
    // define('DB_USER_RW', 'ufunding');
    // define('DB_PASS_RW', 'bieAJs&CS$jfxS7z');

    define('PATH_CRON', '/home/ubuntu/fundingtracker/');
    //TEST
    define('STRIPE_KEY', 'pk_test_51IulAeLiGGzfFa3l7NOOzoQZrx2sNx1HvuRWsA0sx6RQqwKkGqxzFWDiDUDsQ2QzRxUxdLRTMYEjJRqHacUTkMBZ00yXt17gs0');
    define('STRIPE_SECRET', 'sk_test_51IulAeLiGGzfFa3lpFrZLZJpPCoeO7118FQrL4czRLnIZWwHb4oHL8IfSkauQ7DBx7MIEzjs7pYIA6Hq1lNy6KHF00GTp69rby');
}else if ($_SERVER['SERVER_NAME'] == 'megman.fundingtracker.tk') {

    define('DB_CONNECTION_RO', 'ro');
    define('DB_CONNECTION_RW', 'rw');
    define('DB_SERVER', 'development.c7c1e10gnn01.us-east-1.rds.amazonaws.com');
    // define('DB_NAME', 'fundingTrackerDev');
    define('DB_NAME', 'fundingmegaman');
    define('DB_PORT', '3306');
    define('DB_USER_RO', 'udev');
    define('DB_PASS_RO', 'oJEQPK6i4A8q9zbJ');
    define('DB_USER_RW', 'udev');
    define('DB_PASS_RW', 'oJEQPK6i4A8q9zbJ');

    // define('DB_SERVER', 'db-fundingtracker.c7c1e10gnn01.us-east-1.rds.amazonaws.com');
    // define('DB_NAME', 'fundingtracker');
    // define('DB_PORT', '3306');
    // define('DB_USER_RO', 'ufunding');
    // define('DB_PASS_RO', 'bieAJs&CS$jfxS7z');
    // define('DB_USER_RW', 'ufunding');
    // define('DB_PASS_RW', 'bieAJs&CS$jfxS7z');

    define('PATH_CRON', '/home/ubuntu/fundingtracker/');
    //TEST
    define('STRIPE_KEY', 'pk_test_51IulAeLiGGzfFa3l7NOOzoQZrx2sNx1HvuRWsA0sx6RQqwKkGqxzFWDiDUDsQ2QzRxUxdLRTMYEjJRqHacUTkMBZ00yXt17gs0');
    define('STRIPE_SECRET', 'sk_test_51IulAeLiGGzfFa3lpFrZLZJpPCoeO7118FQrL4czRLnIZWwHb4oHL8IfSkauQ7DBx7MIEzjs7pYIA6Hq1lNy6KHF00GTp69rby');
}else if ($_SERVER['SERVER_NAME'] == 'localhost') {

    define('DB_CONNECTION_RO', 'ro');
    define('DB_CONNECTION_RW', 'rw');
    define('DB_SERVER', 'localhost');

    // define('DB_NAME', 'fundingTrackerDev');

    define('DB_NAME', 'fundingtracker');
    define('DB_PORT', '3306');
    define('DB_USER_RO', 'root');
    define('DB_PASS_RO', 'root');
    define('DB_USER_RW', 'root');
    define('DB_PASS_RW', 'root');

    define('PATH_CRON', '/home/ubuntu/fundingtracker/');
    //TEST
    define('STRIPE_KEY', 'pk_test_51IulAeLiGGzfFa3l7NOOzoQZrx2sNx1HvuRWsA0sx6RQqwKkGqxzFWDiDUDsQ2QzRxUxdLRTMYEjJRqHacUTkMBZ00yXt17gs0');
    define('STRIPE_SECRET', 'sk_test_51IulAeLiGGzfFa3lpFrZLZJpPCoeO7118FQrL4czRLnIZWwHb4oHL8IfSkauQ7DBx7MIEzjs7pYIA6Hq1lNy6KHF00GTp69rby');
}else{
    die('Invalid Server Name');
}

/* 
 * Database Constants
 */
// define('DB_CONNECTION_RO', 'ro');
// define('DB_CONNECTION_RW', 'rw');
// define('DB_SERVER', 'localhost');
// define('DB_NAME', 'betafund_funding');
// define('DB_USER_RO', 'root');
// define('DB_PASS_RO', '');
// define('DB_USER_RW', 'root');
// define('DB_PASS_RW', '');





