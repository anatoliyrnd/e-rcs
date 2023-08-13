<?php //build data array
include("include/autoload.php");
use mainSRC\main;
$main = new mainSRC\main();
$main->checkSession();

if (!$main->checkUser()) {
        echo "Ошибка авторизации";
        exit;
    }

if (isset($_REQUEST['id'])) {
    $id = (int)$_REQUEST['id'];
} else {
    echo "Не передан запрос";
    exit();
}
if (isset($_REQUEST['type'])) {
    $type = "/note_thumb/";
}else{
    $type="/note_images/";
}
$data = [];
$image_name=$main->DB->single("SELECT  img_name FROM lift_notes WHERE note_id=? ",array($id));
if ($image_name){
    $dir_image_note=$_SERVER['DOCUMENT_ROOT'] . $type;
    //echo "-".$dir_image_note.$image_name;
    $image = file_get_contents($dir_image_note.$image_name);
   header("Content-type: image/jpeg");
   echo $image;
}
