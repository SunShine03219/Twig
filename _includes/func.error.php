<?php

function add_postback_error($id, $msg){
	$_SESSION['FT']['postback'] = true;
	if(!isset($_SESSION['FT']['postback_errors'])){
		$_SESSION['FT']['postback_errors'] = array();
	}
	$_SESSION['FT']['postback_errors'][$id] = $msg;
}

function add_postback_error_array($array){
	$_SESSION['FT']['postback'] = true;
	foreach($array as $id=>$msg){
		$_SESSION['FT']['postback_errors'][$id]=$msg;
	}
}

function set_postback_msg($msg){
	$_SESSION['FT']['postback'] = true;
	$_SESSION['FT']['postback_msg'] = $msg;
}

function set_success_msg($msg){
	$_SESSION['FT']['success'] = true;
	$_SESSION['FT']['success_msg'] = $msg;
}

function set_postback_data($data){
	$_SESSION['FT']['postback_data'] = array();
	foreach(array_keys($data) as $key){
		$_SESSION['FT']['postback_data'][$key] = $data[$key];
	}
}