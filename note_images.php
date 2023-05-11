<?php //build data array
require_once("./include/session.php");
require_once("./include/checksession.php");
include("./include/ldisp_config.php");
include("./include/function.php");
require_once("./include/PDO.class.php");
if (isset($user_id)) {

    if (nacl($user_id) != $user_nacl) {
        echo "Ошибка авторизации";
        exit;
    }
    $user_id = (int) $user_id;
} else {
    echo "Не известнай пользователь";
    exit();
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
$DB   = new PDODB(db_host, DBPort, db_name, db_user, db_password);
$image_name=$DB->single("SELECT  img_name FROM lift_notes WHERE note_id=? ",array($id));
if ($image_name){

    $dir_image_note=$_SERVER['DOCUMENT_ROOT'] . $type;
    //echo "-".$dir_image_note.$image_name;
    $image = file_get_contents($dir_image_note.$image_name);
   header("Content-type: image/jpeg");
   echo $image;
}
