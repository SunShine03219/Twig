<?php
session_start();
unset($_SESSION['FT']);
unset($_SESSION['open_reg']);
header('location: /');