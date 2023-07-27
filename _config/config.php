<?php
session_start();

if (session_status() !== 2 && !is_array($_SESSION['FT'])) {
    $_SESSION['FT'] = array();
}

date_default_timezone_set('America/Los_Angeles');

/*
 * Load Common Functions
 */
include_once '_includes/func.db.php';
include_once '_includes/func.bouncer.php';
include_once '_includes/func.error.php';
include_once '_includes/func.time.php';