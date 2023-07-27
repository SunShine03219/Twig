<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

/*
 * Autoload Queue
 */
spl_autoload_register(function ($class) {
    include '_includes/classes/' . $class . '_class.php';
});

bounce();

$messages = new messages();
if(isset($_POST['id']) && $_POST['id'] > 0){
    $output = null;
    $id = intval($_POST['id']);
    
    if($messages->make_me_into($id)){
        $messages->set_read_status($id);
        $output = array_merge($messages->to_array(), array('success' => 1));
    }
}else{
    // $arrData = $messages->get_messages_day();
    $arrData = $messages->get_unread_messages();
    
    if(empty($arrData) == false){
        $output['text'] = "";
        $output['title'] = 'New Notification';
        foreach($arrData as $value){
            $date = new DateTime($value['date']);
            $date = $date->format('m/d/Y');
            $output['text'] .= "<div class='notification-title'><h5>" . utf8_decode($value['title']) . "</h5><span>( ". $date ." )</span></div>";
            $output['text'] .= "<p>" . htmlspecialchars_decode($value['text']) . "</p>";
            $output['text'] .= "<br><br>";
            $messages->set_read_status($value['id']);
        }
        $output['success'] = 1;
    }else{
        $output = array('success' => 0);
    }
}

echo json_encode($output);