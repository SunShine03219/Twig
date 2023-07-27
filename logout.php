<?php
session_start();
unset($_SESSION['FT']);
header('location: index.php');